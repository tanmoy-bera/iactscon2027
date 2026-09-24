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
?>
<?php
$loggedUserId		= $mycms->getLoggedUserId();
$prtcpntsArr	 = array();

$buttonAccessArray = array();
$buttonAccessArray['endeveloper'] = 1;
$buttonAccessArray['sourav'] = 15;
$buttonAccessArray['zinia'] = 16;
$buttonAccessArray['tamanna'] = 123;
$buttonAccessArray['cilledbr'] = 6;
$buttonAccessArray['ipsita'] = 119;

$buttonSpPrgAccessArray = array();
?>

<?php
$indexVal          = 1;
$pageKey           = "_pgn" . $indexVal . "_";

$pageKeyVal        = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];
@$searchString     = "";
$searchArray       = array();

$searchArray[$pageKey]                       = $pageKeyVal;
$searchArray['src_name']               		 = addslashes(trim($_REQUEST['src_name']));
$searchArray['src_nationality']        		 = addslashes(trim($_REQUEST['src_nationality']));
$searchArray['src_mobile_no']        		 = addslashes(trim($_REQUEST['src_mobile_no']));
$searchArray['src_email_id']        		 = addslashes(trim($_REQUEST['src_email_id']));
$searchArray['src_field']        			 = addslashes(trim($_REQUEST['src_field']));
$searchArray['src_institution']        		 = addslashes(trim($_REQUEST['src_institution']));
$searchArray['src_alma_mater']        		 = addslashes(trim($_REQUEST['src_alma_mater']));
$searchArray['src_awards']        			 = addslashes(trim($_REQUEST['src_awards']));
$searchArray['src_biodata']        			 = addslashes(trim($_REQUEST['src_biodata']));
$searchArray['src_availibility_date']        = addslashes(trim($_REQUEST['src_availibility_date']));
$searchArray['src_availibility_hour']        = addslashes(trim($_REQUEST['src_availibility_hour']));
$searchArray['src_availibility_min']         = addslashes(trim($_REQUEST['src_availibility_min']));
$searchArray['src_linked']         			 = addslashes(trim($_REQUEST['src_linked']));
$searchArray['src_allocated']         		 = addslashes(trim($_REQUEST['src_allocated']));
$searchArray['src_has_pending_comment']      = addslashes(trim($_REQUEST['src_has_pending_comment']));
$searchArray['src_has_done_comment']         = addslashes(trim($_REQUEST['src_has_done_comment']));
$searchArray['src_participantion']         	 = addslashes(trim($_REQUEST['src_participantion']));
$searchArray['src_participant']         	 = addslashes(trim($_REQUEST['src_participant']));
$searchArray['src_user_tags']         	 	 = addslashes(trim($_REQUEST['src_user_tags']));
$searchArray['src_participantion_status']    = addslashes(trim($_REQUEST['src_participantion_status']));
$searchArray['src_reg_status']    			 = addslashes(trim($_REQUEST['src_reg_status']));
$searchArray['src_has_cv']    				 = addslashes(trim($_REQUEST['src_has_cv']));
$searchArray['src_session_date']         	 	 = addslashes(trim($_REQUEST['src_session_date']));
$searchArray['src_hall_id']         	 	 = addslashes(trim($_REQUEST['src_hall_id']));
$searchArray['src_hall_name']         	 	 = addslashes(trim($_REQUEST['src_hall_name']));

foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}

$buttonAccessArray = array();
$buttonAccessArray['endeveloper'] = 1;
$buttonAccessArray['sourav'] = 15;
$buttonAccessArray['zinia'] = 16;
$buttonAccessArray['tamanna'] = 123;
$buttonAccessArray['cilledbr'] = 6;
$buttonAccessArray['ipsita'] = 119;

$buttonSpPrgAccessArray = array();
//$buttonSpPrgAccessArray['sudipta'] = 20;
//$buttonSpPrgAccessArray['soumyajit'] = 21;
//$buttonSpPrgAccessArray['sujata'] = 12;
?>
<body>
    <style>
        .table_wrap table td {
    color: white;
    padding: 11px;
    border-top: 1px solid var(--border1);
    font-size: 12px;
    background-color: var(--background1);
}
.action_div a, .wrkshp_trak {
    font-size: 12px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: start;
    border-radius: 0;
    padding: 7px;
    gap: 7px;
    cursor: pointer;
    border: 0 !important;
}

</style>
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
                        <li class="breadcrumb-item"><a href="#">Scientific Program</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Manage Participant</a></li>
                    </ol>
                </nav>
                <h2>Manage Participant</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                  <input id="searchInput" name="q" placeholder="Search by Name, Email, Mobile..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="manage_participant.process.php?act=downloadParticipantScheduleExcel" alt="Participant Schedule" title="Participant Schedule">
										<i class="fal fa-file-excel"></i>Participant Schedule
									</a>
                <a  onclick="exportParticipants()" ><?php export(); ?>Export</a>
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newparticipant"><?php add(); ?>New Participant</a>

            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <form method="post" name="frmSearch">
            <div class="filter_body">
                <div class="filter_div">
                    <label>Name</label>
                    <input type="text" name="src_name" id="src_name" style="text-transform:uppercase;" value="<?= $_REQUEST['src_name'] ?>" />
                </div>
                
                <div class="filter_div">
                    <label>Nationality</label>
                    <input type="text" name="src_nationality" id="src_nationality" style="text-transform:uppercase;" value="<?= $_REQUEST['src_nationality'] ?>" />
                </div>
                
                <div class="filter_div">
                  <label>Allocated?</label>
                   <select name="src_allocated" id="src_allocated">
                        <option value="">-- Select --</option>
                        <option value="N" <?= ($_REQUEST['src_allocated'] == 'N') ? 'selected' : '' ?>>NO</option>
                        <option value="Y" <?= ($_REQUEST['src_allocated'] == 'Y') ? 'selected' : '' ?>>YES</option>
                    </select>
                </div>
                 <div class="filter_div">
                    <label>Mobile No:</label>
                    <input type="text" name="src_mobile_no" id="src_mobile_no" style="text-transform:uppercase;" value="<?= $_REQUEST['src_mobile_no'] ?>" />
                </div>
                
                <div class="filter_div">
                    <label>Email Id:</label>
                    <input type="text" name="src_email_id" id="src_email_id" value="<?= $_REQUEST['src_email_id'] ?>" />
                </div>
                
                <div class="filter_div">
                    <label>Linked?</label>
                     <select name="src_linked" id="src_linked">
                        <option value="">-- Select--</option>
                        <option value="N" <?= ($_REQUEST['src_linked'] == 'N') ? 'selected' : '' ?>>NO</option>
                        <option value="Y" <?= ($_REQUEST['src_linked'] == 'Y') ? 'selected' : '' ?>>YES</option>
                    </select>
                 
                </div>
                 <div class="filter_div">
                    <label>Role:</label>
                    <select name="src_participant" id="src_participant">
                        <option value="">-- Select Role --</option>
                        <option value="FACULTY" <?= ($_REQUEST['src_participant'] == 'FACULTY') ? 'selected' : '' ?>>FACULTY</option>
                        <option value="ABSTRACT_PRESENTER" <?= ($_REQUEST['src_participant'] == 'ABSTRACT_PRESENTER') ? 'selected' : '' ?>>ABSTRACT PRESENTER</option>
                        <option value="OTHERS" <?= ($_REQUEST['src_participant'] == 'OTHERS') ? 'selected' : '' ?>>OTHERS</option>
                    </select>
                </div>
                
                <div class="filter_div">
                    <label>Participation:</label>
                    <select name="src_participantion" id="src_participantion">
                        <option value="">-- Select Participation --</option>
                        <?php
                        $sqlParticipantSchdule = array();
                        $sqlParticipantSchdule['QUERY'] = "SELECT DISTINCT participant_type 
                                                            FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
                                                            WHERE participant_type IS NOT NULL
                                                            AND participant_type !=''
                                                            ORDER BY (CASE WHEN LOWER(participant_type)='chairperson' THEN 1
                                                                        WHEN LOWER(participant_type)='moderator' THEN 2
                                                                        WHEN LOWER(participant_type)='panellist' THEN 3
                                                                        WHEN LOWER(participant_type)='speaker' THEN 4
                                                                        ELSE 999999
                                                                    END) ASC, participant_type ASC";
                        $resultsParticipantSchdule = $mycms->sql_select($sqlParticipantSchdule);

                        if ($resultsParticipantSchdule) {
                            foreach ($resultsParticipantSchdule as $key => $rowParticipant) {
                        ?>
                                <option value="<?= $rowParticipant['participant_type'] ?>" <?= ($rowParticipant['participant_type'] == trim($_REQUEST['src_conf_reg_category'])) ? 'selected="selected"' : '' ?>>
                                    <?= $rowParticipant['participant_type'] ?>
                                </option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                 <div class="filter_div">
                    <label>Participation Status:</label>
                    <select name="src_participantion_status">
                        <option value=""> -- Select Status -- </option>
                        <option value="NO-UPDATE" <?= ($_REQUEST['src_participantion_status'] == 'NO-UPDATE') ? "selected" : "" ?>>NO-UPDATE</option>
                        <option value="CONFIRMED" <?= ($_REQUEST['src_participantion_status'] == 'CONFIRMED') ? "selected" : "" ?>>CONFIRMED</option>
                        <option value="NOT-COMING" <?= ($_REQUEST['src_participantion_status'] == 'NOT-COMING') ? "selected" : "" ?>>NOT-COMING</option>
                        <option value="PENDING" <?= ($_REQUEST['src_participantion_status'] == 'PENDING') ? "selected" : "" ?>>PENDING</option>
                        <option value="CONTACT-ISSUE" <?= ($_REQUEST['src_participantion_status'] == 'CONTACT-ISSUE') ? "selected" : "" ?>>CONTACT-ISSUE</option>
                        <option value="MAIL-HOLD" <?= ($_REQUEST['src_participantion_status'] == 'MAIL-HOLD') ? "selected" : "" ?>>MAIL-HOLD</option>
                        <option value="NO-INVLV" <?= ($_REQUEST['src_participantion_status'] == 'NO-INVLV') ? "selected" : "" ?>>NO-INVLV</option>
                    </select>
                </div>
                
                <div class="filter_div">
                    <label>Tags</label>
                    <select name="src_user_tags">
                        <option value=""> -- Select Tags -- </option>
                        <?php
                        $tagInKeys = array();
                        $sqlAllGeneralUser['QUERY'] = "SELECT DISTINCT tags 
                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                        WHERE `status` = 'A'";
                        $resAllGeneralUser = $mycms->sql_select($sqlAllGeneralUser);
                        if ($resAllGeneralUser) {
                            foreach ($resAllGeneralUser as $k => $val) {
                                if (trim($val['tags'] != '')) {
                                    $tagInKeys[$val['tags']] = $val['tags'];
                                }
                            }
                        }
                        foreach ($tagInKeys as $k => $tag) {
                        ?>
                            <option value="<?= $tag ?>" <?= $tag == $searchArray['src_user_tags'] ? 'selected' : '' ?>><?= $tag ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                 <div class="filter_div">
                    <label>Registration Status:</label>
                    <select name="src_reg_status" id="src_reg_status">
                        <option value="">-- Select Registration Status --</option>
                        <option value="Y" <?= ($_REQUEST['src_reg_status'] == 'Y') ? "selected" : "" ?>>Registered</option>
                        <option value="N" <?= ($_REQUEST['src_reg_status'] == 'N') ? "selected" : "" ?>>Not Registered</option>
                    </select>
                </div>
                
                <div class="filter_div">
                    <label>Has CV?</label>
                     <select name="src_has_cv" id="src_has_cv">
                        <option value="">-- Select--</option>
                        <option value="N" <?= ($_REQUEST['src_has_cv'] == 'N') ? 'selected' : '' ?>>NO</option>
                        <option value="Y" <?= ($_REQUEST['src_has_cv'] == 'Y') ? 'selected' : '' ?>>YES</option>
                    </select>
                </div>
                 <div class="filter_div">
                    <label>Has "Pending" Comment</label>
                     <select name="src_has_pending_comment" id="src_has_pending_comment">
                        <option value="">-- Select--</option>
                        <option value="N" <?= ($_REQUEST['src_has_pending_comment'] == 'N') ? 'selected' : '' ?>>NO</option>
                        <option value="Y" <?= ($_REQUEST['src_has_pending_comment'] == 'Y') ? 'selected' : '' ?>>YES</option>
                    </select>
                    <!-- <label>
                        <input type="checkbox" name="src_has_pending_comment" value="Y" <?= $_REQUEST['src_has_pending_comment'] == 'Y' ? 'checked' : '' ?> /> Has "Pending" Comment
                    </label> -->
                </div>
                
                <div class="filter_div">
                    <label>Session Date:</label>
                       <select name="src_session_date" id="src_session_date" style="width:94%;" >
                            <option value="">-- Select Date --</option>
                            <?php
                            $sqlSelectDate = array();
                            $sqlSelectDate['QUERY']		= "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " 
                                                            WHERE `status` = 'A'";

                            $resultDate         = $mycms->sql_select($sqlSelectDate);
                            if ($resultDate) {
                                foreach ($resultDate as $keyDate => $rowDate) {
                            ?>
                                    <option value="<?= $rowDate['id'] ?>" <?= ($_REQUEST['src_session_date']== $rowDate['id']) ? "selected" : "" ?>><?= $rowDate['conf_date'] ?></option>
                            <?php
                                }
                            }
                            ?>
                    </select>
                </div>
                <div class="filter_div">
                    <label>Select Hall</label>
                        <select name="src_hall_id" id="src_hall_id" >
                            <option value="">----Select Hall----</option>
                            <?
                            $sql = array();
                            $sql['QUERY']		 = "SELECT * FROM " . _DB_MASTER_HALL_ . " WHERE status = 'A'";
                            $result	     = $mycms->sql_select($sql);
                            $Counter	 = 0;
                            foreach ($result as $key => $value) {
                            ?>

                                <option value="<?= $value['id'] ?>" <?= ($_REQUEST['src_hall_id']== $value['id']) ? "selected" : "" ?>><?= $value['hall_title'] ?></option>
                            <?
                            }
                            ?>
                        </select>
                </div>
            </div>
            <div class="filter_bottom span_4">
                <button onclick="clearFilters();" ><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
          </form>
        </div>

        <div class="table_wrap">
            <table>
                <thead>
                    <tr>
                        <th class="sl">#</th>
                        <th>Participant</th>
                        <!-- <th>Latest Pending Comment</th> -->
                        <th>Role Of Participation</th>

                        <th>Part. Status</th>
                        <!-- <th class="action">Status</th> -->
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    	@$searchCondition       = "";
                        if ($searchArray['src_name'] != '') {
                            $searchCondition   .= " AND participant_full_name LIKE '%" . $searchArray['src_name'] . "%'";
                        }
                        if($searchArray['src_hall_id'] != '' && $searchArray['src_session_date'] != ''){
                            $searchCondition .= "
                                AND id IN (
                                    SELECT ps.participant_id
                                    FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " ps
                                    INNER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " ss
                                        ON ps.session_id = ss.id
                                    WHERE ss.session_hall_id = '" . $searchArray['src_hall_id'] . "'
                                    AND ss.session_date_id = '" . $searchArray['src_session_date'] . "'
                                )
                            ";
                        }
                        else if($searchArray['src_hall_id'] != '') {

                            $searchCondition .= "
                                AND id IN (
                                    SELECT ps.participant_id
                                    FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " ps
                                    INNER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " ss
                                        ON ps.session_id = ss.id
                                    WHERE ss.session_hall_id = '" . $searchArray['src_hall_id'] . "'
                                )
                            ";
                        }
                        if($searchArray['src_hall_name'] != ''){
                            $sqlGetHallId = array();
                            $sqlGetHallId['QUERY'] = "SELECT `id`,`hall_id`,`date_id` FROM `rcg_program_schedule_hall_name` 
                                                    WHERE `id` = ? AND status = 'A'";
                            $sqlGetHallId['PARAM'][] = array('FILD' => 'id', 'DATA' => $searchArray['src_hall_name'], 'TYP' => 's');
                            $resultHall = $mycms->sql_select($sqlGetHallId);
                        
                            if(!empty($resultHall)) {
                                $hallId = $resultHall[0]['hall_id'];
                                $session_date = $resultHall[0]['date_id'];
                                $searchCondition .= "
                                    AND id IN (
                                        SELECT ps.participant_id
                                        FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " ps
                                        INNER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " ss
                                            ON ps.session_id = ss.id
                                        WHERE ss.session_hall_id = '" . $hallId . "'
                                        AND ss.session_date_id = '" . $session_date. "'
                                    )
                                ";
                            }
                        } 
                        if ($searchArray['src_nationality'] != '') {
                            $searchCondition   .= " AND participant_nationality LIKE '%" . $searchArray['src_nationality'] . "%'";
                        }
                        if ($searchArray['src_mobile_no'] != '') {
                            $searchCondition   .= " AND (participant_mobile_no LIKE '%" . $searchArray['src_mobile_no'] . "%' OR participant_alternative_mobile_no LIKE '%" . $searchArray['src_mobile_no'] . "%' )";
                        }
                        if ($searchArray['src_email_id'] != '') {
                            $searchCondition   .= " AND (participant_email_id LIKE '%" . $searchArray['src_email_id'] . "%'  OR participant_alternative_email_id LIKE '%" . $searchArray['src_email_id'] . "%' )";
                        }
                        if ($searchArray['src_field'] != '') {
                            $searchCondition   .= " AND participant_fields LIKE '%" . $searchArray['src_field'] . "%'";
                        }
                        if ($searchArray['src_institution'] != '') {
                            $searchCondition   .= " AND participant_institutions LIKE '%" . $searchArray['src_institution'] . "%'";
                        }
                        if ($searchArray['src_alma_mater'] != '') {
                            $searchCondition   .= " AND participant_alma_mater LIKE '%" . $searchArray['src_alma_mater'] . "%'";
                        }
                        if ($searchArray['src_awards'] != '') {
                            $searchCondition   .= " AND participant_notable_awards LIKE '%" . $searchArray['src_awards'] . "%'";
                        }
                        if ($searchArray['src_biodata'] != '') {
                            $searchCondition   .= " AND (participant_description LIKE '%" . $searchArray['src_biodata'] . "%' OR participant_career LIKE '%" . $searchArray['src_biodata'] . "%' OR participant_others LIKE '%" . $searchArray['src_biodata'] . "%')";
                        }
                        if ($searchArray['src_participant'] != '') {
                            $searchCondition   .= " AND participation_type = '" . $searchArray['src_participant'] . "'";
                        }
                        if ($searchArray['src_availibility_date'] != '') {
                            if ($searchArray['src_availibility_hour'] == '' && $searchArray['src_availibility_min'] == '') {

                                $searchCondition   .= " AND id IN ( SELECT DISTINCT participant_id
                                                                        FROM " . _DB_SP_PARTICIPANT_AVAILABILITY_ . " avTime
                                                                    INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " avDate
                                                                            ON avDate.id = avTime.available_date_id
                                                                        WHERE `available_date_id` = '" . $searchArray['src_availibility_date'] . "')";
                            } elseif ($searchArray['src_availibility_min'] == '' && is_numeric()) {
                                $searchCondition   .= " AND id IN ( SELECT DISTINCT participant_id
                                                                        FROM " . _DB_SP_PARTICIPANT_AVAILABILITY_ . " avTime
                                                                    INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " avDate
                                                                            ON avDate.id = avTime.available_date_id
                                                                        WHERE `available_date_id` = '" . $searchArray['src_availibility_date'] . "'
                                                                        AND STR_TO_DATE(CONCAT('" . $searchArray['src_availibility_date'] . "',' ','" . number_pad(intval($searchArray['src_availibility_hour']), 2) . "',':00:00'), '%Y-%m-%d %H:%i:%s') 
                                                                            BETWEEN STR_TO_DATE(CONCAT(conf_date,' ',available_start_time,':00'), '%Y-%m-%d %H:%i:%s') 
                                                                            AND STR_TO_DATE((CONCAT(conf_date,' ',available_end_time,':59'), '%Y-%m-%d %H:%i:%s'))";
                            } else {
                                $searchCondition   .= " AND id IN ( SELECT DISTINCT participant_id
                                                                        FROM " . _DB_SP_PARTICIPANT_AVAILABILITY_ . " avTime
                                                                    INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " avDate
                                                                            ON avDate.id = avTime.available_date_id
                                                                        WHERE `available_date_id` = '" . $searchArray['src_availibility_date'] . "'
                                                                        AND STR_TO_DATE(CONCAT('" . $searchArray['src_availibility_date'] . "',' ','" . number_pad(intval($searchArray['src_availibility_hour']), 2) . "',':','" . number_pad(intval($searchArray['src_availibility_min']), 2) . "',':00'), '%Y-%m-%d %H:%i:%s') 
                                                                            BETWEEN STR_TO_DATE(CONCAT(conf_date,' ',available_start_time,':00'), '%Y-%m-%d %H:%i:%s') 
                                                                            AND STR_TO_DATE((CONCAT(conf_date,' ',available_end_time,':59'), '%Y-%m-%d %H:%i:%s'))";
                            }
                        }
                        if ($searchArray['src_linked'] != '' && $searchArray['src_linked'] == 'N') {
                            $searchCondition   .= " AND (participant_delegate_id IS NULL OR participant_delegate_id = '')";
                        }
                        if ($searchArray['src_linked'] != '' && $searchArray['src_linked'] == 'Y') {
                            $searchCondition   .= " AND (participant_delegate_id IS NOT NULL AND participant_delegate_id != '')";
                        }

                        if ($searchArray['src_has_cv'] != '' && $searchArray['src_has_cv'] == 'N') {
                            $searchCondition   .= " AND (participant_description IS NULL OR participant_description = '')";
                        }
                        if ($searchArray['src_has_cv'] != '' && $searchArray['src_has_cv'] == 'Y') {
                            $searchCondition   .= " AND (participant_description IS NOT NULL AND participant_description != '')";
                        }


                        if ($searchArray['src_allocated'] != '') {
                            if ($searchArray['src_allocated'] == 'N') {
                                $searchCondition   .= " AND id NOT IN (SELECT participant_id FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . ")";
                            } elseif ($searchArray['src_allocated'] == 'Y') {
                                $searchCondition   .= " AND id IN (SELECT participant_id FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . ")";
                            }
                        }
                        if ($searchArray['src_has_pending_comment'] != '' && $searchArray['src_has_pending_comment'] == 'Y') {
                            $searchCondition   .= " AND id IN (SELECT participant_id FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " WHERE completionStatus 	= 'INCOMPLETE')";
                        }
                        if ($searchArray['src_has_done_comment'] != '' && $searchArray['src_has_done_comment'] == 'Y') {
                            $searchCondition   .= " AND id IN (SELECT participant_id FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " WHERE completionStatus 	= 'DONE')";
                        }
                        if ($searchArray['src_participantion'] != '') {
                            $searchCondition   .= " AND id IN (SELECT participant_id FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " WHERE participant_type 	= '" . $searchArray['src_participantion'] . "')";
                        }
                        if ($searchArray['src_participantion_status'] != '') {
                            $searchCondition   .= " AND participation_status = '" . $searchArray['src_participantion_status'] . "' ";
                        }
                        if ($searchArray['src_reg_status'] != '') {
                            if ($searchArray['src_reg_status'] == "Y") {
                                $searchCondition   .= " AND participant_delegate_id IN ( SELECT id FROM " . _DB_USER_REGISTRATION_ . " WHERE `status` = 'A' AND isRegistration = 'Y' )";
                            } else if ($searchArray['src_reg_status'] == "N") {
                                $searchCondition   .= " AND participant_delegate_id IN ( SELECT id FROM " . _DB_USER_REGISTRATION_ . " WHERE `status` = 'A' AND isRegistration = 'N' AND registration_request= 'FACULTY' )";
                            }
                        }

                        if ($searchArray['src_user_tags'] != '') {
                            $searchCondition   .= " AND participant_delegate_id IN ( SELECT id FROM " . _DB_USER_REGISTRATION_ . " WHERE `status` = 'A' AND LOCATE('," . $searchArray['src_user_tags'] . ",', CONCAT(',',tags,',') ) > 0 )";
                        }
                       if (!empty($_GET['q'])) {

                            $q = trim($_GET['q']);
                            $words = preg_split('/\s+/', $q);

                            $wordConditions = [];

                            foreach ($words as $word) {

                                $word = addslashes($word); // you can later upgrade to prepared

                                $wordConditions[] = "(
                                    participant_full_name LIKE '%$word%' 
                                    OR participant_email_id LIKE '%$word%' 
                                    OR participant_mobile_no LIKE '%$word%' 
                                    OR participant_alternative_mobile_no LIKE '%$word%'
                                    OR participant_delegate_id LIKE '%$word%'
                                )";
                            }

                            // Each word must match somewhere
                            $searchCondition .= " AND (" . implode(" AND ", $wordConditions) . ")";
                        }
                        /////for searching///////
                        $sqlListingSearch = array();
                        $sqlListingSearch['QUERY']		 = "SELECT *
                                                            FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
                                                            WHERE `status` = ? " . $searchCondition . "
                                                        ORDER BY TRIM(participant_full_name)";

                        $sqlListingSearch['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');

                        //echo '<!--'; print_r($sqlListing); echo '-->';

                        $resultsListingSearch	 = $mycms->sql_select($sqlListingSearch);

                        // echo '<pre>'; print_r($resultsListing);						


                        $prtcpntsArrSearch	 = array();
                        foreach ($resultsListingSearch as $key2 => $rowDetailsSearch) {
                            $prtcpntsArrSearch[] = $rowDetailsSearch['id'];
                           
                        }
                        ////////////////////////
                    $sqlListing = array();
                    $sqlListing['QUERY']		 = "SELECT *
                                                        FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
                                                        WHERE `status` = ? " . $searchCondition . "
                                                    ORDER BY TRIM(participant_full_name)";

                    $sqlListing['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');

                    $resultsListing	 = $mycms->sql_select_paginated('R001',$sqlListing,50);
                        $perPage = 50; // IMPORTANT: must match SQL LIMIT

                        $pageIndex = isset($_GET['_pgnR001_'])
                            ? (int)$_GET['_pgnR001_']
                            : 0;

                        $offset = $pageIndex * $perPage;
						$counter = 0;
						if ($resultsListing) {
							foreach ($resultsListing as $key => $rowDetails) {
                                		$prtcpntsArr[] = $rowDetails['id'];

                                  $countertest = $offset + $key + 1;
								 $counter      = $countertest;
								$dispUserFlag = "";
								$tagText 	= array();

								//echo '<pre>'; print_r($rowDetails);

								$sqlProcessUpdateStep = array();
								$sqlProcessUpdateStep['QUERY']       = "UPDATE  " . _DB_USER_REGISTRATION_ . "
																		   SET `roles` 		= ?
																			  
																		 WHERE `id` = ?";
								$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'roles',          'DATA' => $rowDetails['participation_type'],     				 		'TYP' => 's');

								$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',               'DATA' => $rowDetails['participant_delegate_id'],	'TYP' => 's');
								$mycms->sql_update($sqlProcessUpdateStep, false);

								if ($rowDetails['participant_registration_id'] != "" && $rowDetails['participant_unique_sequence'] != "") {

									$rowUserRegDet = getUserDetails($rowDetails['participant_delegate_id']);
									$regClassfName = getRegClsfName($rowUserRegDet['registration_classification_id']);
									$rowGetUserIdByEmail = getUserDetailsByEmail($rowDetails['participant_email_id']);

									if ($rowUserRegDet['registration_classification_id'] >= 7) {
										$userFlag = "home";
									} else {
										$userFlag = "user";
									}

									if ($rowUserRegDet['registration_payment_status'] >= 'UNPAID') {
										$payFlag = "RED";
									} else {
										$payFlag = "GREEN";
									}

									switch ($userFlag) {
										case "user":
											$dispUserFlag = '<i class="fa fa-user-md" aria-hidden="true" style="color:' . $payFlag . '" title="' . $regClassfName . '"></i>';
											break;
										case "home":
											$dispUserFlag = '<i class="fa fa-home" aria-hidden="true" style="color:' . $payFlag . '" title="' . $regClassfName . '"></i>';
											break;
									}

									$array 		= $rowUserRegDet['tags'];
									$var 		= (explode(",", $array));
									foreach ($var as $key => $val) {
										if ($val == 'Executive Committee') {
											$tagText[] = '<span style="color:#990033;"><b>' . $val . '</b></span>';
										}
										if ($val == 'Organizing Committee Member') {
											$tagText[] = '<span style="color:#009966;"><b>' . $val . '</b>&nbsp;</span>';
										}
										if ($val == 'Guest Faculty') {
											$tagText[] = '<span style="color:#CC3333;"><b>' . $val . '</b>&nbsp;</span>';
										}
										if ($val == 'Special Faculty') {
											$tagText[] = '<span style="color:#FF0066;"><b>' . $val . '</b>&nbsp;</span>';
										}
										if ($val == 'Regional Faculty') {
											$tagText[] = '<span style="color:#007700;"><b>' . $val . '</b>&nbsp;</span>';
										}
										if ($val == 'National Faculty') {
											$tagText[] = '<span style="color:#660066;"><b>' . $val . '</b>&nbsp;</span>';
										}
										if ($val == 'International Faculty') {
											$tagText[] = '<span style="color:#770000;"><b>' . $val . '</b></span>';
										}
										if ($val == 'Special Guest') {
											$tagText[] = '<span style="color:#663399;"><b>' . $val . '</b>&nbsp;</span>';
										}
									}
								}


						?>
                    <tr>
                        <td class="sl"><?= $counter ?></td>
                        <td>
                            <div class="regi_name"><?= strtoupper($rowDetails['participant_full_name']) ?></div>
                            
                            <div class="regi_contact">
                                <?php
                                //echo '<pre>'; print_r($rowUserRegDet) $rowDetails['participant_title'] . " " . ;
                                if ($rowDetails['participant_registration_id'] != "" && $rowDetails['participant_unique_sequence'] != "" && $rowUserRegDet['registration_request'] == 'GENERAL') {
                                ?>
                                    <span>Reg. Id: <?= $rowDetails['participant_registration_id'] ?></span>
                                    <span><?php qr() ?><?= $rowDetails['participant_unique_sequence'] ?></span> 
                                <?php
                                } elseif (in_array($loggedUserId, $buttonAccessArray)) {
                                ?>
                                    <a href="manage_participant.process.php?act=linkUpParticipant&id=<?= $rowDetails['id'] ?><?= $searchString ?>">
                                        <i class="fa fa-link" aria-hidden="true" style="color:#0033FF" title="Link with EMAIL"></i>
                                    </a>
                                    <a href="manage_participant.process.php?act=linkUpParticipantMobile&id=<?= $rowDetails['id'] ?><?= $searchString ?>">
                                        <i class="fa fa-link" aria-hidden="true" style="color:#FF9900" title="Link with MOBILE"></i>
                                    </a>
                                <?
                                }
                                ?>
                                <span>
                                    <?php call(); ?><?= $rowDetails['participant_mobile_no'] ?>
                                </span>
                                
                                <span>
                                    <?php email(); ?><?= $rowDetails['participant_email_id'] ?>
                                </span>
                                <? if($rowDetails['participant_alternative_email_id'] !=''){?>
                                <span>
                                     <?php email(); ?><?= $rowDetails['participant_alternative_email_id'] ?>
                                </span>
                                <?} ?>
                                <?
                                $sqlDuplicate = array();
                                $sqlDuplicate['QUERY']	 = "SELECT id 
                                                            FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
                                                            WHERE `status` = 'A' 
                                                            AND `participant_email_id` = '" . $rowDetails['participant_email_id'] . "'
                                                            AND `id` != '" . $rowDetails['id'] . "'
                                                            AND `participant_email_id` NOT LIKE '%@ruedakolkata.com'";
                                $resultsDuplicate	 = $mycms->sql_select($sqlDuplicate);
                                if ($resultsDuplicate) {
                                ?>
                                    <span style="font-size:10px; color:#FF0000; font-weight:bold;">Duplicate</span>
                                <?
                                }
                                ?>
                                  <?
										$sqlParticipantSchdule = array();
										$sqlParticipantSchdule['QUERY']		 = "SELECT DISTINCT participant_type 
																				  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
																				 WHERE `participant_id` = '" . $rowDetails['id'] . "'
																			  ORDER BY (CASE WHEN LOWER(participant_type)='chairperson' THEN 1
																			  				 WHEN LOWER(participant_type)='moderator' THEN 2
																							 WHEN LOWER(participant_type)='panellist' THEN 3
																							 WHEN LOWER(participant_type)='speaker' THEN 4
																							 ELSE 999999
																			  			END) ASC, participant_type ASC";
										$resultsParticipantSchdule	 = $mycms->sql_select($sqlParticipantSchdule);

										$participations = array();

										foreach ($resultsParticipantSchdule as $key => $rowDetail) {
											$participations[] = $rowDetail['participant_type'];
										}
                                     ?>
                                 <span>
                                   <? if (!empty($participations)) { ?>
                                    <?php user(); ?>Role : <?= $rowDetails['participation_type'] ?>
                                    <? }else{?>
                                     <?php user(); ?>Role : NONE
                                    <? } ?>
                                </span>
                            </div>
                        </td>
                        <!-- <td><?
                            $sqlComent = array();
                            $sqlComent['QUERY'] 	   = "    SELECT participant_comment.*, 
                                                                        recordingUser.name AS recoderName, completingUser.name As completerName
                                                                FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
                                                        LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
                                                                    ON participant_comment.recordedBy = recordingUser.a_id
                                                        LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
                                                                    ON participant_comment.completedBy = completingUser.a_id
                                                                WHERE participant_comment.participant_id 	= '" . $rowDetails['id'] . "'
                                                                    AND participant_comment.completionStatus 	= 'INCOMPLETE'
                                                            ORDER BY participant_comment.id DESC
                                                                LIMIT 1";
                            $resComent		= $mycms->sql_select($sqlComent);
                            $rowComment		= $resComent[0];
                            echo nl2br($rowComment['comment']);
                            ?>
                        </td> -->
                        <td>
                          <?
										if (!empty($participations)) {
                                            echo "<strong>Participation : </strong>" . implode(", ", $participations);
                                           } ?>
                        </td>
                        <td>
                            <form use='settingParticipantStatus' action="manage_participant.process.php" method="post">
                                <input type="hidden" name="act" value="updateParticipation" />
                                <input type="hidden" name="paricipantId" value="<?= $rowDetails['id'] ?>" />
                                <?php
                                foreach ($searchArray as $key => $val) {
                                ?>
                                    <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
                                <?php
                                }
                                ?>
                                <select name="participationStatus" onchange="updateParticipationStatus(this);" participantId="<?= $rowDetails['id'] ?>">
                                    <option value="NO-UPDATE" <?= ($rowDetails['participation_status'] == 'NO-UPDATE') ? "selected" : "" ?>>NO-UPDATE</option>
                                    <option value="CONFIRMED" <?= ($rowDetails['participation_status'] == 'CONFIRMED') ? "selected" : "" ?>>CONFIRMED</option>
                                    <option value="NOT-COMING" <?= ($rowDetails['participation_status'] == 'NOT-COMING') ? "selected" : "" ?>>NOT-COMING</option>
                                    <option value="PENDING" <?= ($rowDetails['participation_status'] == 'PENDING') ? "selected" : "" ?>>PENDING</option>
                                    <option value="CONTACT-ISSUE" <?= ($rowDetails['participation_status'] == 'CONTACT-ISSUE') ? "selected" : "" ?>>CONTACT-ISSUE</option>
                                    <option value="MAIL-HOLD" <?= ($rowDetails['participation_status'] == 'MAIL-HOLD') ? "selected" : "" ?>>MAIL-HOLD</option>
                                    <option value="NO-INVLV" <?= ($rowDetails['participation_status'] == 'NO-INVLV') ? "selected" : "" ?>>NO-INVLV</option>
                                </select>
                            </form>
                        </td>
                        <!-- <td>
                            <div class="action_div">
                                <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                            </div>
                        </td> -->
                        <td class="action">
                            <div class="action_div">
                                <a data-tab="editparticipent" data-id="<?=$rowDetails['id']?>"  class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editparticipentBtn"><?php edit(); ?></a>
                                <?
                                $sqlEmails = array();
                                $sqlEmails['QUERY']		 = "SELECT `emailType` FROM " . _DB_SP_PARTICIPANT_SCHEDULE_MAIL_ . "  WHERE `participantId` = '" . $rowDetails['id'] . "' ORDER BY id DESC";
                                $resultsEmails	 = $mycms->sql_select($sqlEmails);
                                $rowEmails		 = $resultsEmails[0];
                                if ($rowEmails && $rowEmails['emailType']=='Invitation Mail') {
                                ?>
                                <a href="participant_send_mail.php?&id=<?= $rowDetails['id'] ?><?= $searchString ?>" class="icon_hover badge_info br-5 w-auto action-transparent" target="_blank" style="color:#0033FF"><i style="color:#f668d4;" class="fal fa-envelope"></i></a>
                                <? } else if($rowEmails && $rowEmails['emailType']!='Invitation Mail'){ ?>
                                    <a href="participant_send_mail.php?&id=<?= $rowDetails['id'] ?><?= $searchString ?>" class="icon_hover badge_info br-5 w-auto action-transparent" target="_blank" style="color:#0033FF" ><i style="color:#5aff59;" class="fal fa-envelope"></i></a>

                                 <? }else{ ?>
                                    <a href="participant_send_mail.php?&id=<?= $rowDetails['id'] ?><?= $searchString ?>" class="icon_hover badge_info br-5 w-auto action-transparent" target="_blank" ><?php email() ?></a>

                                <? }   ?>
                              

                                 <a href="<?= _BASE_URL_ ?>webmaster/tags_registration.php?src_access_key=<?= str_replace('#', '', $rowDetails['participant_unique_sequence']) ?>" style="color:#000000;" target="_blank">
												<i class="fa fa-tags" aria-hidden="true" style="color:#e7d0d0;" title="Tag"></i>
											</a>
                                <a href="manage_participant_comment.php?&id=<?= $rowDetails['id'] ?><?= $searchString ?>" style="color:#000000;" target="_blank">
                                    <i class="fa-solid fa-comment" aria-hidden="true" style="color:#e7d0d0"></i>
                                </a><br>

                            </div>
                             <div  style="
                                    justify-content: end;
                                    display: flex;
                                    gap: 3px;
                                    padding-right: 10px;
                                ">
                                    <a style="justify-content: right;" href="manage_participant.process.php?act=removeParticipant&id=<?= $rowDetails['id'] ?>"  onclick="return confirm('Do you really want to remove this record?')" class="badge_danger icon_hover action-transparent"><?php delete(); ?></a>

                                    <!-- <a  href="manage_participant.process.php?act=removeParticipant&id=<?= $rowDetails['id'] ?> class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a> -->
                            </div>
                        </td>
                    </tr>
                    <? } } ?>
                </tbody>
            </table>
            <script>
                function updateParticipationStatus(obj) {
                    console.log("trigger updateParticipationStatus");



                    var parent = $(obj).parent().closest("form");

                    $(parent).submit();


                    /*
                    */
                }

                $(document).on('click', '#participant_feature', function() {

                    var id = $(this).attr('attr-id');

                    if ($(this).is(':checked')) {
                        var flag = 1;
                    } else {
                        var flag = 0;
                    }

                    //alert(id);

                    if (id != '' && id > 0) {

                        $.ajax({
                            type: "POST",
                            url: 'manage_participant.process.php',
                            data: 'act=updateParticipantFeature&id=' + id + '&flag=' + flag,
                            dataType: 'json',
                            async: false,
                            success: function(JSONObject) {
                                console.log(JSONObject);


                                if (JSONObject.succ == 200) {



                                }

                            }
                        });
                    }


                });



                $(document).ready(function() {
                    $('form[use=settingParticipantStatus]').submit(function(e) {
                        e.preventDefault();


                        var parent = $(this);
                        var path = $(parent).attr("action");

                        console.log("hitting >> " + path);

                        $.ajax({
                            type: "POST",
                            url: path,
                            data: $(parent).serialize(),
                            dataType: "text",
                            async: false,
                            success: function(JSONObject) {
                                console.log("ran updateParticipationStatus");
                                alert('Data updated successfully');
                                //window.location.reload();
                            }
                        });
                    });
                });
            </script>
        </div>
        <div class="bbp-pagination">
            <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo('R001') ?></div>
            <span class="paginationDisplay">
                <div class="pagination"><a><?= $mycms->paginate('R001', 'pagination') ?></a></div>
            </span>
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
      ///////////////////Section Edit///////////////////////////
   $(document).on('click', '.editparticipentBtn', function () {

    let participentId = $(this).data('id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { participentId: participentId },
        success: function (response) {

            $('#editparticipent').html($(response).find('#editparticipent').html());

            // Re-initialize after DOM replacement
            initeditparticipent();
               $('#editparticipent').fadeIn();

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
       ///////////////////Section edit end///////////////////////////
function clearFilters() {
    // Get the form
    var form = document.forms['frmSearch'];

    // Clear selects
    form.src_name.value = "";
    form.src_nationality.value = "";
    form.src_allocated.value = "";
    form.src_mobile_no.value = "";
    form.src_email_id.value = "";
    form.src_linked.value = "";
    form.src_participant.value = "";
    form.src_participantion.value = "";
    form.src_participantion_status.value = "";
    form.src_user_tags.value = "";
    form.src_has_cv.value = "";
    form.src_reg_status.value = "";
    form.src_has_pending_comment.value = "";
    form.src_has_done_comment.value = "";
    // Clear date input
    form.querySelector('input[type="date"]').value = "";

    // Optional: clear hidden act value (if you want a clean GET)
    // form.act.value = "";

    // Submit the form to PHP with empty values
    form.submit();
}
</script>
<script>


const searchInput = document.getElementById("searchInput");
let typingTimer;
const typingDelay = 800; // wait 0.8s after last keystroke

searchInput.addEventListener("keyup", function() {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        const query = this.value.trim();

        const url = new URL(window.location.href);

        if (query.length > 0) {
            url.searchParams.set('q', query);
        } else {
            url.searchParams.delete('q');
        }

        url.searchParams.delete('_pgnR001_'); // reset pagination
        window.location.href = url.toString(); // reload page with new query
    }, typingDelay);
});

searchInput.addEventListener("keydown", () => clearTimeout(typingTimer));

</script>
</html>
<?php
// After the foreach loop that populates $prtcpntsArrSearch
$allIdsJson = json_encode($prtcpntsArrSearch);
?>
<script>
    var allParticipantIds = <?= $allIdsJson ?>;
    
    function exportParticipants() {
        if (allParticipantIds.length === 0) {
            alert('No participants to export');
            return;
        }
        window.location.href = 'manage_participant.process.php?act=downloadParticipantExcel&participants=' + allParticipantIds.join(',');
    }
</script>