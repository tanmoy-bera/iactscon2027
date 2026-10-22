<?php include_once("includes/source.php"); ?>

<body>
    <style>
    /* Red styling for exceeding elements */
    .exceeding-theme {
        border: 2px solid red !important;
        background-color: rgba(255, 0, 0, 0.05);
    }
    .exceeding-topic {
        color: red !important;
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
                        <li class="breadcrumb-item active" aria-current="page">Program Schedule</li>
                    </ol>
                </nav>
                <h2>Program Schedule</h2>
                <h6>Manage conference sessions and speakers.</h6>
            </div>
        </div>
        <div class="regi_search_wrap mb-3">
            <div class="tracking_analytic_tab">
             <?php
                $sqlDateisting = array();
                $sqlDateisting['QUERY']		= "SELECT * FROM "._DB_PROGRAM_SCHEDULE_DATE_." WHERE `status` = ?";
                $sqlDateisting['PARAM'][]  	= array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');
                $resultDateListing   		= $mycms->sql_select($sqlDateisting);
                $Counter			 		= 0;
             
                if($resultDateListing)
                {
                    $firstButton = true;
                    foreach($resultDateListing as $keyDateListing=>$value){
                        if ($firstButton) {
                            $buttonClass = 'active';
                            $firstButton = false;
                        }else{
                            $buttonClass = '';
                        }
                ?>
    
                <button data-tab="tab-<?=$value['id']?>" class="tabBtn <?=$buttonClass?>"><?=date('Y-m-d',strtotime($value['conf_date']))?><a href="full_program_schedule.process.php?act=downloadScheduleExcel&dateId=<?=$value['id']?>" data-dateid="<?=$value['id']?>"  class="tracking_analytic_tab_a ml-2"><i class="fal fa-file-excel"></i></a></i></button>
                 <? } } ?>
            </div>
           <div class="regi_search_wrap_btn_box">
                 <a href="full_program_schedule.process.php?act=downloadScheduleExcel" ><i class="fal fa-file-excel"></i>Download Excel</a>
                <a href="full_program_schedule.process.php?act=getPDFDisplay" target="_blank" ><i class="fas fa-file-pdf"></i>Download Pdf</a>
                <a href="javascript:void(null)" data-tab="newsession"  data-dateid="<?= $firstDateId ?>"  data-hallid="<?= $firstHallId ?>" class="popup-btn add newsessionBtn"><?php add(); ?>New
                    Session</a>
            </div>
        </div>
        
        <? if($resultDateListing)
                {
                    foreach($resultDateListing as $keyDateListing=>$value){

                ?>
        <div class="tracking_analytic_box active" id="tab-<?=$value['id']?>">
        <div class="pgrm_hall_tab_wrap">
                 <?
                    $sql = array();
                    $sql['QUERY']		 = "SELECT hallData.*, IFNULL(hallTempname.hall_name, hallData.hall_title) AS hall_title
                                                FROM "._DB_MASTER_HALL_." hallData
                                    LEFT OUTER JOIN "._DB_MASTER_HALL_NAME_." hallTempname
                                                ON hallData.id = hallTempname.hall_id
                                                AND hallTempname.date_id = '".$value['id']."'
                                                WHERE hallData.status = 'A'  
                                            ORDER BY hallData.hall_title";
                                                                                            
                       
                    $resultHall  = $mycms->sql_select($sql);
                  
                    $occupancyWidth = $genericWidth-5;
                    $greatGrandPasWidth = sizeof($resultHall)*$genericWidth+10;	
                 ?>
                <div class="pgrm_hall_tab_box">
                    <div class="pgrm_hall_tab">
                  <?
                    if($resultHall)
                    {
                      $firstButtonHall = true;

                        foreach($resultHall as $key=>$rowHall)
                        {		
                        if ($firstButtonHall) {
                            $buttonClassHall = 'active';
                            $firstButtonHall = false;
                        }else{
                            $buttonClassHall = '';
                        }						
						?>
                    
                        <a href="#<?=$rowHall['id']?><?=$value['id']?>"  data-hall-id="<?=$rowHall['id']?>" class="<?=$buttonClassHall?>"><?=$rowHall['hall_title']?></a>
                   
                     <? } } ?>
                      </div>
                </div>
               
            </div>
            <div class="pg_shdl_wrap">
                  <?
					
                    $sql = array();
                    $sql['QUERY']		 = "SELECT hallData.*, IFNULL(hallTempname.hall_name, hallData.hall_title) AS hall_title
                                                FROM "._DB_MASTER_HALL_." hallData
                                    LEFT OUTER JOIN "._DB_MASTER_HALL_NAME_." hallTempname
                                                ON hallData.id = hallTempname.hall_id
                                                AND hallTempname.date_id = '".$value['id']."'
                                                WHERE hallData.status = 'A'  
                                            ORDER BY hallData.hall_title";
                                                                                            
                       
                    $resultHall  = $mycms->sql_select($sql);
                    $occupancyWidth = $genericWidth-5;
                    $greatGrandPasWidth = sizeof($resultHall)*$genericWidth+10;	
                     
                    if($resultHall)
                    {
                         
                         $firstButtonHallGrp = true;
                        foreach($resultHall as $key=>$rowHall)
                        {			
                        if ($firstButtonHallGrp) {
                            $buttonClassHallgrp = 'active';
                              $buttonClassHallgrpStyle = 'display:block;';
                            $firstButtonHallGrp = false;
                        }else{
                            $buttonClassHallgrpStyle = '';

                            $buttonClassHallgrp = '';
                        }			
                       
						?>
                        
                <div class="pg_shdl_box <?=$buttonClassHallgrp?>"   data-hall-id="<?=$rowHall['id']?>" style="<?=$buttonClassHallgrpStyle?>" id="<?=$rowHall['id']?><?=$value['id']?>">
                    <h4 class="pg_shdl_box_head"><span><?php address() ?> <?=$rowHall['hall_title']?></span>
                        <!-- <button title="Expand" class="pg_shdl_box_expand"><i class="fal fa-expand-wide"></i></button> -->
                    </h4>
                    <ul class="pg_shdl_box_ul">
                         <?  	
                        $participantTypes = array("chairperson","moderator","panellist","speaker");
                        $sqlSelectSession = array();
                        $sqlSelectSession['QUERY']        = "SELECT session.*,
                                                                (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60) AS session_start_time_mins,
                                                                (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)-1 AS session_end_time_mins,
                                                                hall.hall_title,
                                                                venue.id AS venue_id,
                                                                venue.program_venue,
                                                                scheduleDate.conf_date AS session_date  
                                                            
                                                            FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
                                                            
                                                        INNER JOIN "._DB_PROGRAM_SCHEDULE_DATE_." scheduleDate 
                                                                ON session.session_date_id = scheduleDate.id
                                                                
                                                        INNER JOIN "._DB_MASTER_HALL_." hall 
                                                                ON session.session_hall_id = hall.id
                                                                
                                                        INNER JOIN "._DB_PROGRAM_SCHEDULE_VENUE_." venue 
                                                                ON hall.hall_venue = venue.id
                                                                
                                                            WHERE session.status = 'A' 
                                                            AND session.session_date_id = '".$value['id']."'
                                                            AND session.session_hall_id = '".$rowHall['id']."'
                                                                ".$searchCondition."
                                                        ORDER BY (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60), (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)";
                                                        
                        //print_r($sqlSelectSession);
                          
                         
                        $resultSession			 = $mycms->sql_select($sqlSelectSession);
                       
                        if($resultSession)
                        {
                            $minStartTime 		 = 24*60;
                            $maxEndTime   		 = 0;
                            $overlap	  		 = array();
                            
                            $composedScheduleData = array();
                            // echo "<pre>";print_r($resultSession);
                            foreach($resultSession as $keySchedule=>$rowSession)
                            {
                                $startTimeMins = $rowSession['session_start_time_mins'];				
                                $endTimeMins = $rowSession['session_end_time_mins'];
                                
                                if($startTimeMins < $minStartTime)
                                {
                                    $minStartTime = $startTimeMins;
                                    $composedScheduleData['minStartTime'] = $minStartTime;
                                }
                                
                                
                                if($endTimeMins > $maxEndTime)
                                {
                                    $maxEndTime = $endTimeMins;
                                    $composedScheduleData['maxEndTime'] = $maxEndTime;
                                }
                                
                                                
                                if(sizeof($overlap) > 0)
                                {
                                    $hasOverlapped = false;
                                    foreach($overlap as $ke => $spans)
                                    {
                                        foreach($spans as $k=>$span)
                                        {
                                            $spanStart  	= $span['start'];
                                            $spanEnd 		= $span['end'];
                                            $hall 			= $span['hall'];
                                            $isOverlapping	= false;
                                            
                                            if($startTimeMins>=$spanStart && $startTimeMins < $spanEnd)
                                            {
                                                $isOverlapping = true;
                                            }
                                            elseif($endTimeMins>=$spanStart && $endTimeMins < $spanEnd)
                                            {
                                                $isOverlapping = true;
                                            }
                                            elseif($spanStart>=$startTimeMins && $spanStart < $endTimeMins)
                                            {
                                                $isOverlapping = true;
                                            }
                                            elseif($spanEnd>=$startTimeMins && $spanEnd < $endTimeMins)
                                            {
                                                $isOverlapping = true;
                                            }
                                            
                                            if($isOverlapping)
                                            {
                                                if($ke == (sizeof($overlap)-1))
                                                {
                                                    $overlap[sizeof($overlap)][] = array("start"=>$startTimeMins,"end"=>$endTimeMins,"hall"=>$rowSession['session_hall_id']);
                                                    $hasOverlapped = true;
                                                    $composedScheduleData[(sizeof($overlap)-1)][$keySchedule] = $rowSession;
                                                    $composedScheduleData[(sizeof($overlap)-1)][$keySchedule]['leftMarginAspect'] = (sizeof($overlap)-1);
                                                }
                                                else
                                                {
                                                    break;
                                                }
                                            }
                                            else
                                            {
                                                $overlap[$ke][] = array("start"=>$startTimeMins,"end"=>$endTimeMins,"hall"=>$rowSession['session_hall_id']);
                                            }
                                        }
                                        if($hasOverlapped)
                                        {
                                            break;
                                        }
                                        if(!$isOverlapping)
                                        {
                                            $composedScheduleData[$ke][$keySchedule] = $rowSession;
                                            $composedScheduleData[$ke][$keySchedule]['leftMarginAspect'] = $ke;
                                            break;
                                        }
                                    }
                                }
                                else
                                {
                                    $overlap[0][] = array("start"=>$startTimeMins,"end"=>$endTimeMins,"hall"=>$rowSession['session_hall_id']);
                                    $composedScheduleData[0][$keySchedule] = $rowSession;
                                    $composedScheduleData[0][$keySchedule]['leftMarginAspect'] = 0;
                                }
                                
                                $composedScheduleData['session_overlaps'] = sizeof($overlap);
                                $composedScheduleData['occupancyWidth'] = round(100/sizeof($overlap))-1;				
                            }
                            
                            foreach($composedScheduleData as $k=>$scheduleData)
                            {
                                if(is_numeric($k))
                                {
                                    foreach($scheduleData as $keySchedule=>$rowSession)
                                        {
                                            if(is_numeric($keySchedule))
                                            {
                                                // Calculate session duration in minutes
                                                $sessionStart = strtotime($rowSession['session_start_time']);
                                                $sessionEnd = strtotime($rowSession['session_end_time']);
                                                $sessionDurationMinutes = ($sessionEnd - $sessionStart) / 60;
                                                
                                                $sessionCounter++;	?>
                        <li class="pg_shdl_box_ul_li"  style="border-color: <?=$rowSession['session_color']?>">
                            <div class="pg_shdl_box_ul_li_box">
                                <div class="pg_shdl_details">
                                    <div class="pg_shdl_time">
                                        <?php clock() ?>
                                        <p><span><b><?=$rowSession['session_start_time']?></b><br><?=$rowSession['session_end_time']?></span></p>
                                    </div>
                                    <div class="pg_shdl_name">
                                        <span class="badge_padding" style="color: <?=$rowSession['session_color']?>">Session</span>
                                        <div class="regi_name"><?=$rowSession['session_title']?></div>
                                        <j>
                                        <?php
                                        $sqlParticipantTheme = array();
                                        $sqlParticipantTheme['QUERY'] = "
                                            SELECT prt.*, sch.participant_type
                                            FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
                                            INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
                                                ON sch.participant_id = prt.id
                                            WHERE sch.session_id = '".$rowSession['id']."'
                                            AND (sch.theme_id IS NULL OR sch.theme_id = '')
                                            AND (sch.topic_id IS NULL OR sch.topic_id = '')
                                            ORDER BY sch.participant_type, prt.participant_full_name
                                        ";

                                        $resultsParticipantTheme = $mycms->sql_select($sqlParticipantTheme);

                                        if ($resultsParticipantTheme) {

                                            $groupedParticipants = array();

                                            // Group names by participant_type
                                            foreach ($resultsParticipantTheme as $rowParticipant) {

                                                $type = trim($rowParticipant['participant_type']);
                                                $name = trim($rowParticipant['participant_full_name']);

                                                if ($name != '') {
                                                    $groupedParticipants[$type][] = $name;
                                                }
                                            }

                                            // Display
                                            foreach ($groupedParticipants as $type => $names) {
                                                ?>
                                                <span style="display:block;">
                                                    <b style="color: var(--default2); font-weight:500;">
                                                        <?= $type ?> :
                                                    </b>
                                                    <?= implode(', ', $names) ?>
                                                </span>
                                                <?php
                                            }
                                        }
                                        ?>

                                        </j>
                                    </div>
                                    <button class="pg_shdl_btn active"><?php view() ?></button>
                                </div>
                                 <div class="action_div action">
                                    <a href="javascript:void(null)" data-tab="editsession" data-id="<?=$rowSession['id']?>"  class="icon_hover badge_secondary br-5 w-auto action-transparent popup-btn editsessionBtn"><?php edit() ?></a>
                                    <a href="javascript:void(null)" class="icon_hover badge_danger br-5 w-auto action-transparent"  onclick="sessionRemover(<?=$rowSession['id']?>);" ><?php delete() ?></a>
                                </div>
                            </div>
                             <?
                            $sqlFetchTheme = array(); 
                            $sqlFetchTheme['QUERY']           = "   SELECT *, 
                                                                        (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60) AS theme_time_start_mins,
                                                                        (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)-1 AS theme_time_end_mins
                                                                    FROM "._DB_PROGRAM_SCHEDULE_THEME_." 
                                                                    WHERE schedule_id = '".$rowSession['id']."'
                                                                    AND status = 'A'  
                                                                ORDER BY (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60), (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)";													 
                            $resultTheme   = $mycms->sql_select($sqlFetchTheme);	
                            
                           
                              ?>
                            <div class="schdl_grp_ul">
                                <?  if($resultTheme && $resultTheme[0]['noTheme']=='N')
                                 { ?>
                               <div class="schdl_grp_wrap">
                                    <?    
                                    $composedThemeData = array();
                                    $overlap	  	   = array();
                                    // Initialize cumulative theme duration tracker
                                    $cumulativeThemeDuration = 0;
                                    
                                    foreach($resultTheme as $keyTheme=>$rowTheme)
                                    {
                                        $startTimeMins = $rowTheme['theme_time_start_mins'];				
                                        $endTimeMins = $rowTheme['theme_time_end_mins'];
                                        
                                        // Calculate theme duration
                                        $themeStart = strtotime($rowTheme['theme_time_start']);
                                        $themeEnd = strtotime($rowTheme['theme_time_end']);
                                        $themeDurationMinutes = ($themeEnd - $themeStart) / 60;
                                        
                                        // Add to cumulative theme duration
                                        $cumulativeThemeDuration += $themeDurationMinutes;
                                        
                                        if($startTimeMins < $minStartTime)
                                        {
                                            $minStartTime = $startTimeMins;
                                            $composedThemeData['minStartTime'] = $minStartTime;
                                        }
                                        
                                        if($endTimeMins > $maxEndTime)
                                        {
                                            $maxEndTime = $endTimeMins;
                                            $composedThemeData['maxEndTime'] = $maxEndTime;
                                        }
                                        
                                        
                                        if(sizeof($overlap) > 0)
                                        {
                                            $hasOverlapped = false;
                                            foreach($overlap as $ke => $spans)
                                            {
                                                foreach($spans as $k=>$span)
                                                {
                                                    $spanStart  	= $span['start'];
                                                    $spanEnd 		= $span['end'];
                                                    $isOverlapping	= false;
                                                    
                                                    if($startTimeMins>=$spanStart && $startTimeMins < $spanEnd)
                                                    {
                                                        $isOverlapping = true;
                                                    }
                                                    elseif($endTimeMins>=$spanStart && $endTimeMins < $spanEnd)
                                                    {
                                                        $isOverlapping = true;
                                                    }
                                                    elseif($spanStart>=$startTimeMins && $spanStart < $endTimeMins)
                                                    {
                                                        $isOverlapping = true;
                                                    }
                                                    elseif($spanEnd>=$startTimeMins && $spanEnd < $endTimeMins)
                                                    {
                                                        $isOverlapping = true;
                                                    }
                                                    
                                                    if($isOverlapping)
                                                    {
                                                        if($ke == (sizeof($overlap)-1))
                                                        {
                                                            $overlap[sizeof($overlap)][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                            $hasOverlapped = true;
                                                            $composedThemeData[(sizeof($overlap)-1)][$keyTheme] = $rowTheme;
                                                            $composedThemeData[(sizeof($overlap)-1)][$keyTheme]['leftMarginAspect'] = (sizeof($overlap)-1);
                                                            $composedThemeData[(sizeof($overlap)-1)][$keyTheme]['cumulativeDuration'] = $cumulativeThemeDuration;
                                                            $composedThemeData[(sizeof($overlap)-1)][$keyTheme]['themeDuration'] = $themeDurationMinutes;
                                                        }
                                                        else
                                                        {
                                                            break;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $overlap[$ke][] =  array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                    }
                                                }
                                                if($hasOverlapped)
                                                {
                                                    break;
                                                }
                                                if(!$isOverlapping)
                                                {
                                                    $composedThemeData[$ke][$keyTheme] = $rowTheme;
                                                    $composedThemeData[$ke][$keyTheme]['leftMarginAspect'] = $ke;
                                                    $composedThemeData[$ke][$keyTheme]['cumulativeDuration'] = $cumulativeThemeDuration;
                                                    $composedThemeData[$ke][$keyTheme]['themeDuration'] = $themeDurationMinutes;
                                                    break;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $overlap[0][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                            $composedThemeData[0][$keyTheme] = $rowTheme;
                                            $composedThemeData[0][$keyTheme]['leftMarginAspect'] = 0;
                                            $composedThemeData[0][$keyTheme]['cumulativeDuration'] = $cumulativeThemeDuration;
                                            $composedThemeData[0][$keyTheme]['themeDuration'] = $themeDurationMinutes;
                                        }
                                        
                                        $composedThemeData['session_overlaps'] = sizeof($overlap);
                                        $composedThemeData['occupancyWidth'] = round(100/sizeof($overlap))-2;				
                                    }							
                                    
                                    foreach($composedThemeData as $kt=>$themeData)
                                    {
                                        if(is_numeric($kt))
                                        {
                                            foreach($themeData as $keyTheme=>$rowtheme)
                                                { 
                                                    // Get cumulative and theme duration
                                                    $cumulativeDuration = isset($rowtheme['cumulativeDuration']) ? $rowtheme['cumulativeDuration'] : 0;
                                                    $themeDuration = isset($rowtheme['themeDuration']) ? $rowtheme['themeDuration'] : 0;
                                                    
                                                    // Check if cumulative themes exceed session duration
                                                    $isThemeExceeding = ($cumulativeDuration > $sessionDurationMinutes);
                                                    $themeExceedClass = $isThemeExceeding ? 'exceeding-theme' : '';
                                                    $themeExceedStyle = $isThemeExceeding ? 'style="border:2px solid red !important;"' : '';
                                                    $themeTextStyle = $isThemeExceeding ? 'style="color:red !important;"' : '';
                                                    ?>
                                    <div class="schdl_grp <?= $themeExceedClass ?>" <?= $themeExceedStyle ?>>
                                        <? if($rowtheme['noTheme']=='N'){ ?>
                                        <div class="pg_shdl_action">
                                            <div class="partidipent_grp">
                                                <div class="partidipent_grp_time">
                                                    <?php clock() ?>
                                                    <p><span><b <?= $themeTextStyle ?>><?=$rowtheme['theme_time_start']?></b><br <?= $themeTextStyle ?>><?=$rowtheme['theme_time_end']?></span></p>
                                                </div>
                                                <div class="partidipent_grp_name">
                                                    <span <?= $themeTextStyle ?>><?=$rowtheme['theme_title']?></span>
                                                    <j>
                                                        <?php
                                                            $sqlParticipantTheme = array();
                                                            $sqlParticipantTheme['QUERY'] = "
                                                                SELECT prt.*, sch.participant_type
                                                                FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
                                                                INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
                                                                    ON sch.participant_id = prt.id
                                                                WHERE sch.session_id = '".$rowtheme['schedule_id']."'
                                                                AND sch.theme_id = '".$rowtheme['id']."'
                                                                AND (sch.topic_id IS NULL OR sch.topic_id = '')
                                                            ";

                                                            $resultsParticipantTheme = $mycms->sql_select($sqlParticipantTheme);

                                                            $participantGroups = array();

                                                            if ($resultsParticipantTheme)
                                                            {
                                                                foreach ($resultsParticipantTheme as $rowParticipant)
                                                                {
                                                                    $name = trim($rowParticipant['participant_full_name']);
                                                                    $type = trim($rowParticipant['participant_type']);

                                                                    if ($name != '')
                                                                    {
                                                                        $participantGroups[$type][] = $name;
                                                                    }
                                                                }
                                                            }

                                                            if (!empty($participantGroups))
                                                            {
                                                                foreach ($participantGroups as $type => $names)
                                                                {
                                                                    ?>
                                                                    <span style="display:block;">
                                                                        <b style="color: var(--default2); font-weight:500;">
                                                                            <?= $type ?> :
                                                                        </b>
                                                                        <?= implode(', ', $names) ?>
                                                                    </span>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                    </j>
                                                </div>
                                            </div>
                                           <div class="action_div action">
                                                 <a href="javascript:void(null)" class="icon_hover badge_danger br-5 w-auto action-transparent" onclick="themeRemover(<?=$rowtheme['id']?>);"><?php delete() ?></a>
                                            </div>
                                        </div>
                                        <? } ?>
                                        <ul class="pg_shdl_talk_box">
                                            <?php
                                            $sqlFetchTopic = array();
                                            $sqlFetchTopic['QUERY']           = "   SELECT *, 
                                                                                        topic_time_duration AS topic_time_duration_mins,
                                                                                        (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
                                                                                        (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins
                                                                                    FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." 
                                                                                    WHERE schedule_session_id = '".$rowSession['id']."'
                                                                                    AND schedule_theme_id = '".$rowtheme['id']."'
                                                                                    AND status = 'A'
                                                                                ORDER BY sequence, id";													 
                                            $resultTopic = $mycms->sql_select($sqlFetchTopic);	
                                            
                                            // Initialize cumulative topic duration for this theme
                                            $cumulativeTopicDuration = 0;
                                            
                                            if($resultTopic)
                                            { 
                                                $composedTopicData = array();
                                                $overlap = array();
                                                foreach($resultTopic as $keyTopic=>$rowTopic)
                                                {
                                                    $startTimeMins = $rowTopic['topic_time_start_mins'];				
                                                    $endTimeMins = $rowTopic['topic_time_end_mins'];
                                                    
                                                    if($startTimeMins < $minStartTime)
                                                    {
                                                        $minStartTime = $startTimeMins;
                                                    }
                                                    $composedTopicData['minStartTime'] = $minStartTime;
                                                    
                                                    if($endTimeMins > $maxEndTime)
                                                    {
                                                        $maxEndTime = $endTimeMins;
                                                    }
                                                    $composedTopicData['maxEndTime'] = $maxEndTime;
                                                    
                                                    if(sizeof($overlap) > 0)
                                                    {
                                                        $hasOverlapped = false;
                                                        foreach($overlap as $ke => $spans)
                                                        {
                                                            foreach($spans as $k=>$span)
                                                            {
                                                                $spanStart  	= $span['start'];
                                                                $spanEnd 		= $span['end'];
                                                                $isOverlapping	= false;
                                                                
                                                                if($startTimeMins>=$spanStart && $startTimeMins < $spanEnd)
                                                                {
                                                                    $isOverlapping = true;
                                                                }
                                                                elseif($endTimeMins>=$spanStart && $endTimeMins < $spanEnd)
                                                                {
                                                                    $isOverlapping = true;
                                                                }
                                                                elseif($spanStart>=$startTimeMins && $spanStart < $endTimeMins)
                                                                {
                                                                    $isOverlapping = true;
                                                                }
                                                                elseif($spanEnd>=$startTimeMins && $spanEnd < $endTimeMins)
                                                                {
                                                                    $isOverlapping = true;
                                                                }
                                                                
                                                                if($isOverlapping)
                                                                {
                                                                    if($ke == (sizeof($overlap)-1))
                                                                    {
                                                                        $overlap[sizeof($overlap)][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                                        $hasOverlapped = true;
                                                                        $composedTopicData[(sizeof($overlap)-1)][$keyTopic] = $rowTopic;
                                                                        $composedTopicData[(sizeof($overlap)-1)][$keyTopic]['leftMarginAspect'] = (sizeof($overlap)-1);
                                                                    }
                                                                    else
                                                                    {
                                                                        break;
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    $overlap[$ke][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                                }
                                                            }
                                                            if($hasOverlapped)
                                                            {
                                                                break;
                                                            }
                                                            if(!$isOverlapping)
                                                            {
                                                                $composedTopicData[$ke][$keyTopic] = $rowTopic;
                                                                $composedTopicData[$ke][$keyTopic]['leftMarginAspect'] = $ke;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $overlap[0][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                        $composedTopicData[0][$keyTopic] = $rowTopic;
                                                        $composedTopicData[0][$keyTopic]['leftMarginAspect'] = 0;
                                                    }
                                                    
                                                    $composedTopicData['session_overlaps'] = sizeof($overlap);
                                                    $composedTopicData['occupancyWidth'] = round(100/sizeof($overlap))-2;				
                                                }		
                                                
                                                foreach($composedTopicData as $kt=>$topicData)
                                                {
                                                    if(is_numeric($kt))
                                                    { 
                                                        foreach($topicData as $keyTopic=>$rowTopic)
                                                        { 
                                                            // Add topic duration to cumulative
                                                            $topicDuration = (int)$rowTopic['topic_time_duration'];
                                                            $cumulativeTopicDuration += $topicDuration;
                                                            
                                                            // Check if cumulative topics exceed theme duration
                                                            $isTopicExceeding = ($cumulativeTopicDuration > $themeDuration);
                                                            $topicExceedClass = $isTopicExceeding ? 'exceeding-topic' : '';
                                                            $topicExceedStyle = $isTopicExceeding ? 'style="color:red !important;"' : '';
                                                            ?>
                                            <li class="pg_shdl_talk_box_li">
                                                <div class="pg_shdl_talk_box_li_left">
                                                    <n class="<?= $topicExceedClass ?>" <?= $topicExceedStyle ?>><?=$rowTopic['topic_time_duration']?> m <br><?=$rowTopic['reference_tag']?></n>
                                                    <k><g><span class="<?= $topicExceedClass ?>" <?= $topicExceedStyle ?>><?=$rowTopic['topic_title']?></span></g>
                                                    <j>
                                                    <?php
                                                        $sqlParticipantTheme = array();
                                                        $sqlParticipantTheme['QUERY'] = "
                                                            SELECT prt.*, sch.participant_type
                                                            FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
                                                            INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
                                                                ON sch.participant_id = prt.id
                                                            WHERE sch.session_id = '".$rowTopic['schedule_session_id']."'
                                                            AND sch.theme_id = '".$rowTopic['schedule_theme_id']."'
                                                            AND sch.topic_id = '".$rowTopic['id']."'
                                                            ORDER BY sch.participant_type, prt.participant_full_name
                                                        ";

                                                        $resultsParticipantTheme = $mycms->sql_select($sqlParticipantTheme);

                                                        $participantGroups = array();

                                                        if ($resultsParticipantTheme)
                                                        {
                                                            foreach ($resultsParticipantTheme as $rowParticipant)
                                                            {
                                                                $name = trim($rowParticipant['participant_full_name']);
                                                                $type = trim($rowParticipant['participant_type']);

                                                                if ($name != '')
                                                                {
                                                                    $participantGroups[$type][] = $name;
                                                                }
                                                            }
                                                        }

                                                        if (!empty($participantGroups))
                                                        {
                                                            foreach ($participantGroups as $type => $names)
                                                            {
                                                                ?>
                                                                <span style="display:block;">
                                                                    <b style="color: var(--default2); font-weight:500;">
                                                                        <?= $type ?> :
                                                                    </b>
                                                                    <?= implode(', ', $names) ?>
                                                                </span>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </j>
                                                   </k>
                                                </div>

                                            </li>
                                             <? } } } }?>
                                        </ul>
                                    </div>
                                    <? } } }?>
                                </div>
                                <? }else{
                                     ?>
                                <div class="pg_shdl_tals_wrap">
                                    <?php    
                                    $composedThemeData = array();
                                    $overlap = array();
                                    
                                    // Initialize cumulative topic duration for this session (noTheme = Y)
                                    $cumulativeDuration = 0;
                                    
                                    foreach($resultTheme as $keyTheme=>$rowTheme)
                                    {
                                        $startTimeMins = $rowTheme['theme_time_start_mins'];				
                                        $endTimeMins = $rowTheme['theme_time_end_mins'];
                                        
                                        if($startTimeMins < $minStartTime)
                                        {
                                            $minStartTime = $startTimeMins;
                                            $composedThemeData['minStartTime'] = $minStartTime;
                                        }
                                        
                                        if($endTimeMins > $maxEndTime)
                                        {
                                            $maxEndTime = $endTimeMins;
                                            $composedThemeData['maxEndTime'] = $maxEndTime;
                                        }
                                        
                                        
                                        if(sizeof($overlap) > 0)
                                        {
                                            $hasOverlapped = false;
                                            foreach($overlap as $ke => $spans)
                                            {
                                                foreach($spans as $k=>$span)
                                                {
                                                    $spanStart  	= $span['start'];
                                                    $spanEnd 		= $span['end'];
                                                    $isOverlapping	= false;
                                                    
                                                    if($startTimeMins>=$spanStart && $startTimeMins < $spanEnd)
                                                    {
                                                        $isOverlapping = true;
                                                    }
                                                    elseif($endTimeMins>=$spanStart && $endTimeMins < $spanEnd)
                                                    {
                                                        $isOverlapping = true;
                                                    }
                                                    elseif($spanStart>=$startTimeMins && $spanStart < $endTimeMins)
                                                    {
                                                        $isOverlapping = true;
                                                    }
                                                    elseif($spanEnd>=$startTimeMins && $spanEnd < $endTimeMins)
                                                    {
                                                        $isOverlapping = true;
                                                    }
                                                    
                                                    if($isOverlapping)
                                                    {
                                                        if($ke == (sizeof($overlap)-1))
                                                        {
                                                            $overlap[sizeof($overlap)][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                            $hasOverlapped = true;
                                                            $composedThemeData[(sizeof($overlap)-1)][$keyTheme] = $rowTheme;
                                                            $composedThemeData[(sizeof($overlap)-1)][$keyTheme]['leftMarginAspect'] = (sizeof($overlap)-1);
                                                        }
                                                        else
                                                        {
                                                            break;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $overlap[$ke][] =  array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                    }
                                                }
                                                if($hasOverlapped)
                                                {
                                                    break;
                                                }
                                                if(!$isOverlapping)
                                                {
                                                    $composedThemeData[$ke][$keyTheme] = $rowTheme;
                                                    $composedThemeData[$ke][$keyTheme]['leftMarginAspect'] = $ke;
                                                    break;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $overlap[0][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                            $composedThemeData[0][$keyTheme] = $rowTheme;
                                            $composedThemeData[0][$keyTheme]['leftMarginAspect'] = 0;
                                        }
                                        
                                        $composedThemeData['session_overlaps'] = sizeof($overlap);
                                        $composedThemeData['occupancyWidth'] = round(100/sizeof($overlap))-2;				
                                    }							
                                    
                                    foreach($composedThemeData as $kt=>$themeData)
                                    {
                                        if(is_numeric($kt))
                                        {
                                            foreach($themeData as $keyTheme=>$rowtheme)
                                                { ?>
                                    <ul class="pg_shdl_talk_box">
                                         <?php
                                            $sqlFetchTopic = array();
                                            $sqlFetchTopic['QUERY']           = "   SELECT *, 
                                                                                        topic_time_duration AS topic_time_duration_mins,
                                                                                        (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
                                                                                        (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins
                                                                                    FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." 
                                                                                    WHERE schedule_session_id = '".$rowSession['id']."'
                                                                                    AND schedule_theme_id = '".$rowtheme['id']."'
                                                                                    AND status = 'A'
                                                                                ORDER BY sequence, id";													 
                                            $resultTopic   			 = $mycms->sql_select($sqlFetchTopic);	
                                            
                                            if($resultTopic)
                                            { 
                                                $composedTopicData = array();
                                                $overlap = array();
                                                foreach($resultTopic as $keyTopic=>$rowTopic)
                                                {
                                                    $startTimeMins = $rowTopic['topic_time_start_mins'];				
                                                    $endTimeMins = $rowTopic['topic_time_end_mins'];
                                                    
                                                    if($startTimeMins < $minStartTime)
                                                    {
                                                        $minStartTime = $startTimeMins;
                                                    }
                                                    $composedTopicData['minStartTime'] = $minStartTime;
                                                    
                                                    if($endTimeMins > $maxEndTime)
                                                    {
                                                        $maxEndTime = $endTimeMins;
                                                    }
                                                    $composedTopicData['maxEndTime'] = $maxEndTime;
                                                    
                                                    if(sizeof($overlap) > 0)
                                                    {
                                                        $hasOverlapped = false;
                                                        foreach($overlap as $ke => $spans)
                                                        {
                                                            foreach($spans as $k=>$span)
                                                            {
                                                                $spanStart  	= $span['start'];
                                                                $spanEnd 		= $span['end'];
                                                                $isOverlapping	= false;
                                                                
                                                                if($startTimeMins>=$spanStart && $startTimeMins < $spanEnd)
                                                                {
                                                                    $isOverlapping = true;
                                                                }
                                                                elseif($endTimeMins>=$spanStart && $endTimeMins < $spanEnd)
                                                                {
                                                                    $isOverlapping = true;
                                                                }
                                                                elseif($spanStart>=$startTimeMins && $spanStart < $endTimeMins)
                                                                {
                                                                    $isOverlapping = true;
                                                                }
                                                                elseif($spanEnd>=$startTimeMins && $spanEnd < $endTimeMins)
                                                                {
                                                                    $isOverlapping = true;
                                                                }
                                                                
                                                                if($isOverlapping)
                                                                {
                                                                    if($ke == (sizeof($overlap)-1))
                                                                    {
                                                                        $overlap[sizeof($overlap)][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                                        $hasOverlapped = true;
                                                                        $composedTopicData[(sizeof($overlap)-1)][$keyTopic] = $rowTopic;
                                                                        $composedTopicData[(sizeof($overlap)-1)][$keyTopic]['leftMarginAspect'] = (sizeof($overlap)-1);
                                                                    }
                                                                    else
                                                                    {
                                                                        break;
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    $overlap[$ke][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                                }
                                                            }
                                                            if($hasOverlapped)
                                                            {
                                                                break;
                                                            }
                                                            if(!$isOverlapping)
                                                            {
                                                                $composedTopicData[$ke][$keyTopic] = $rowTopic;
                                                                $composedTopicData[$ke][$keyTopic]['leftMarginAspect'] = $ke;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $overlap[0][] = array("start"=>$startTimeMins,"end"=>$endTimeMins);
                                                        $composedTopicData[0][$keyTopic] = $rowTopic;
                                                        $composedTopicData[0][$keyTopic]['leftMarginAspect'] = 0;
                                                    }
                                                    
                                                    $composedTopicData['session_overlaps'] = sizeof($overlap);
                                                    $composedTopicData['occupancyWidth'] = round(100/sizeof($overlap))-2;				
                                                }		
                                                
                                                foreach($composedTopicData as $kt=>$topicData)
                                                {
                                                    if(is_numeric($kt))
                                                    { 
                                                        foreach($topicData as $keyTopic=>$rowTopic)
                                                        { 
                                                            // Add topic duration to cumulative
                                                            $topicDuration = (int)$rowTopic['topic_time_duration'];
                                                            $cumulativeDuration += $topicDuration;
                                                            
                                                            // Check if cumulative topics exceed session duration
                                                            $isTopicExceeding = ($cumulativeDuration > $sessionDurationMinutes);
                                                            $topicExceedClass = $isTopicExceeding ? 'exceeding-topic' : '';
                                                            $topicExceedStyle = $isTopicExceeding ? 'style="color:red !important;"' : '';
                                                            ?>
                                        <li class="pg_shdl_talk_box_li">
                                            <div class="pg_shdl_talk_box_li_left">
                                                <n class="<?= $topicExceedClass ?>" <?= $topicExceedStyle ?>><?=$rowTopic['topic_time_duration']?> m <br><?=$rowTopic['reference_tag']?></n>
                                                <k> <g><span class="<?= $topicExceedClass ?>" <?= $topicExceedStyle ?>><?=$rowTopic['topic_title']?></span></g>
                                                <j>
                                                   <?php
                                                        $sqlParticipantTheme = array();
                                                        $sqlParticipantTheme['QUERY'] = "
                                                            SELECT prt.*, sch.participant_type
                                                            FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
                                                            INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
                                                                ON sch.participant_id = prt.id
                                                            WHERE sch.session_id = '".$rowTopic['schedule_session_id']."'
                                                            AND sch.theme_id = '".$rowTopic['schedule_theme_id']."'
                                                            AND sch.topic_id = '".$rowTopic['id']."'
                                                            ORDER BY sch.participant_type, prt.participant_full_name
                                                        ";

                                                        $resultsParticipantTheme = $mycms->sql_select($sqlParticipantTheme);

                                                        $participantGroups = array();

                                                        if ($resultsParticipantTheme)
                                                        {
                                                            foreach ($resultsParticipantTheme as $rowParticipant)
                                                            {
                                                                $name = trim($rowParticipant['participant_full_name']);
                                                                $type = trim($rowParticipant['participant_type']);

                                                                if ($name != '')
                                                                {
                                                                    $participantGroups[$type][] = $name;
                                                                }
                                                            }
                                                        }

                                                        if (!empty($participantGroups))
                                                        {
                                                            foreach ($participantGroups as $type => $names)
                                                            {
                                                                ?>
                                                                <span style="display:block;">
                                                                    <b style="color: var(--default2); font-weight:500;">
                                                                        <?= $type ?> :
                                                                    </b>
                                                                    <?= implode(', ', $names) ?>
                                                                </span>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                </j>
                                                </k>
                                            </div>

                                        </li>
                                           <? } } } }?>
                                    </ul>
                                     <? } } }?>
                                </div>
                               <? } ?>
                            </div>
                        </li>
                         <? } } } } }?>
                    </ul>
                </div>
                <? } } ?>
            </div>
        </div>
        <? } } ?>
    </div>
    <?php include_once("includes/popup.php"); ?>
    <?php
        // Store the first date ID and hall ID for the current tab
        $firstDateId = $resultDateListing[0]['id'] ?? '';
        $firstHallId = $resultHall[0]['id'] ?? '';
        ?>

</body>
<?php include_once("includes/js-source.php"); ?>
<script>
   
     $(".pgrm_hall_tab a[href^='#']").click(function (e) {
        e.preventDefault();
         $(".pgrm_hall_tab a").removeClass("active");
        $(this).addClass("active");
        $('.pg_shdl_box').hide();
        $($(this).attr("href")).show();
    });
    $('.added_grp_expand').click(function () {
        $(this).parent().parent().toggleClass('active');
            $(this).toggleClass('active');
    });
    $('.pg_shdl_btn').click(function () {
        $(this).parent().parent().parent().find('.schdl_grp_ul').slideToggle();
        $(this).toggleClass('active');
    });
     $(document).ready(function() {
        // Initialize first hall as selected
        $('.tabBtn.active').trigger('click');
        
        // Get first date and hall IDs for New Session button
        var $activeTab = $('.tracking_analytic_box.active');
        if ($activeTab.length) {
            var dateId = $activeTab.attr('id').replace('tab-', '');
            var $firstHall = $activeTab.find('.pg_shdl_box').first();
            var hallId = $firstHall.data('hall-id');
            $('.newsessionBtn').data('dateid', dateId);
            $('.newsessionBtn').data('hallid', hallId);
        }
    });
    function sessionRemover(Id) {
    if (confirm('Are you sure you want to remove this session?')) {
        $.ajax({
            type: "POST",
            url: "full_program_schedule.process.php",
            data: {
                act: 'removeSession',
                sessionId: Id
            },
            dataType: "html",
            success: function(HTMLObject) {
              window.location.href = "program_schedule.php";

            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    }
}
function themeRemover(themeid)
{
 if (confirm('Are you sure you want to remove this Group?')) {

    setTimeout( function(){

        var themeId		 = themeid;			
        $.ajax({
            type: "POST",
            url: "full_program_schedule.process.php",
            data: {
                act: 'removeTheme',
                themeId: themeId
            },
            dataType: "html",
            success: function(HTMLObject){						
                window.location.href = "program_schedule.php";
            }
        });
    },100);
  }
}
    function topicRemover(topicId)
    {
            if (confirm('Are you sure you want to remove this topic?')) {

    
        setTimeout( function(){
            var valTopicId			= topicId;
            
            $.ajax({
                type: "POST",
                url: "full_program_schedule.process.php",
                data: {
                    act: 'removeTopic',
                    topic_id: valTopicId
                },
                dataType: "html",
                success: function(HTMLObject){						
                    window.location.href = "program_schedule.php";
                }
            });
        },100);
    }
	}
    function deleteThemeParticipant(obj) {
     if (confirm('Are you sure you want to remove this Group participant?')) {

        setTimeout(function() {
            var valThemeId = $(obj).attr('data_themeId');
            var valParticipantId = $(obj).attr('data_participantId');
            $.ajax({
                type: "POST",
                url: "full_program_schedule.process.php",
                data: 'act=removeThemeParticipant&themeId=' + valThemeId + '&participantId=' + valParticipantId,
                dataType: "html",
                async: true,
                success: function(JSONObject) {
                   window.location.href = "program_schedule.php";
                }
            });
        }, 100);
    }
    }
    function deleteTopicParticipant(obj) {
     if (confirm('Are you sure you want to remove this topic participant?')) {

        setTimeout(function() {
            var valTopicId = $(obj).attr('data_topicId');
            var valParticipantId = $(obj).attr('data_participantId');
            $.ajax({
                type: "POST",
                url: "full_program_schedule.process.php",
                data: 'act=removeTopicParticipant&topicId=' + valTopicId + '&participantId=' + valParticipantId,
                dataType: "html",
                async: true,
                success: function(JSONObject) {
                   window.location.href = "program_schedule.php";
                }
            });
        }, 100);
     }
    }
    $(document).on('click', '.sessionparticipentsBtn', function() {
        var themeId     = $(this).data('id');
      
         
        $('#themeId').val(themeId);
       
        // Trigger the popup-btn functionality
     $('#sessionparticipents').fadeIn(); // or your popup open function
    });
    $(document).on('click', '.topicparticipentsBtn', function() {
        var topicId     = $(this).data('id');
      
         
        $('#topicId').val(topicId);
       
        // Trigger the popup-btn functionality
     $('#topicparticipents').fadeIn(); // or your popup open function
    });

   $(document).on('click', '.newsessionBtn', function() {
        var dateId = $(this).data('dateid') || '';
        var hallId = $(this).data('hallid') || '';
        // alert(hallId);
        // Send these values to the popup
        $('#session_date').val(dateId);
         $('#hall_id').val(hallId);
        
        // Trigger the popup
        $('#newsession').fadeIn();
    });

  $(document).on('click', '.editsessionBtn', function () {

    let sessionIdEdit = $(this).data('id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { sessionIdEdit: sessionIdEdit },
        success: function (response) {

            $('#editsession').html($(response).find('#editsession').html());

            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditsession();
           

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
/////////////////scrlling/
$(document).ready(function() {
    // Function to get current active container
    function getActiveContainer() {
        return $('.tracking_analytic_box.active');
    }
    
    // Function to close all halls in current container
    function closeAllHallsInContainer($container) {
        $container.find('.pg_shdl_box').removeClass('expand');
        // $container.find('.pgrm_hall_tab a').removeClass('active');
    }
    
    // Function to expand specific hall
    function expandHallInContainer($container, hallName) {
        var $hallSection = $container.find('.pg_shdl_box').filter(function() {
            var title = $(this).find('.pg_shdl_box_head span').text().trim();
            return title === hallName;
        });
        
        if ($hallSection.length) {
            closeAllHallsInContainer($container);
            $hallSection.addClass('expand');
            
            $container.find('.pgrm_hall_tab a').each(function() {
                if ($(this).text().trim() === hallName) {
                    $(this).addClass('active');
                }
            });
            
            // Scroll to hall
            var headerHeight = $('header').outerHeight() || 80;
            var topBarHeight = $('.page_top_wrap').outerHeight() || 60;
            var scrollPosition = $hallSection.offset().top - (headerHeight + topBarHeight + 30);
            $('html, body').animate({
                scrollTop: scrollPosition
            }, 400);
            
            return true;
        }
        return false;
    }
    
    // Function to collapse specific hall
    function collapseHallInContainer($container, hallName) {
        var $hallSection = $container.find('.pg_shdl_box').filter(function() {
            var title = $(this).find('.pg_shdl_box_head span').text().trim();
            return title === hallName;
        });
        
        if ($hallSection.length) {
            $hallSection.removeClass('expand');
            $container.find('.pgrm_hall_tab a').each(function() {
                if ($(this).text().trim() === hallName) {
                    $(this).removeClass('active');
                }
            });
            return true;
        }
        return false;
    }
    
    // Handle hall tab clicks - TOGGLE functionality
    $(document).on('click', '.pgrm_hall_tab a', function(e) {
        e.preventDefault();
        
        var $activeContainer = getActiveContainer();
        var $this = $(this);
        var hallName = $this.text().trim();
        var href = $(this).attr('href');
        var hallId =  $(this).data('hall-id');
        var $activeTab = $('.tracking_analytic_box.active');
        var dateId = $activeTab.attr('id').replace('tab-', '');
        
        // Update the New Session button
        $('.newsessionBtn').data('dateid', dateId);
        $('.newsessionBtn').data('hallid', hallId);
        // Check if this tab belongs to the active container
        if ($this.closest('.tracking_analytic_box').is($activeContainer)) {
            
            // Check if this hall is currently expanded
            var $currentHall = $activeContainer.find('.pg_shdl_box').filter(function() {
                var title = $(this).find('.pg_shdl_box_head span').text().trim();
                return title === hallName;
            });
            
            if ($currentHall.hasClass('expand')) {
                // Hall is expanded - collapse it
                collapseHallInContainer($activeContainer, hallName);
            } else {
                // Hall is collapsed - expand it (and close others)
                expandHallInContainer($activeContainer, hallName);
            }
        }
        
    });
    
    // Handle date tab switching - reset all halls
  $(document).on('click', '.tabBtn', function () {
    let $this = $(this);
    let tabId = $this.data('tab');

    setTimeout(function () {
        var dateId = tabId.replace('tab-', '');
  
        // hide all halls in all tabs
        $('.pg_shdl_box').removeClass('active').hide();

        // show FIRST hall of ACTIVE DATE only
        let $activeTab = $('.tracking_analytic_box.active');

        let $firstHall = $activeTab.find('.pg_shdl_box').first();
        var hallId = $firstHall.data('hall-id');

        $firstHall.addClass('active').show();

        // activate first tab link
        let firstId = $firstHall.attr('id');

        $activeTab.find('.pgrm_hall_tab a').removeClass('active');

        $activeTab.find('.pgrm_hall_tab a[href="#' + firstId + '"]').addClass('active');
        $('.newsessionBtn').data('dateid', dateId);
        $('.newsessionBtn').data('hallid', hallId);
    }, 100);
});
    
    // Initialize - no hall expanded on load
    var $initialContainer = getActiveContainer();
    closeAllHallsInContainer($initialContainer);
});
$(document).ready(function() {
    // Helper to read a query param from the URL
    function getUrlParam(name) {
        var params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    var urlDateId = getUrlParam('dateId');
    var urlHallId = getUrlParam('hallId');

    var savedDateId = urlDateId || localStorage.getItem('schedule_active_dateId');
    var savedHallId = urlHallId || localStorage.getItem('schedule_active_hallId');

    var $targetDateTab = savedDateId ? $('.tabBtn[data-tab="tab-' + savedDateId + '"]') : null;

    if ($targetDateTab && $targetDateTab.length) {
        $targetDateTab.trigger('click');
    } else {
        $('.tabBtn.active').trigger('click');
    }

    setTimeout(function() {
        var $activeTab = $('.tracking_analytic_box.active');
        if (!$activeTab.length) return;

        var dateId = $activeTab.attr('id').replace('tab-', '');
        var $targetHall = savedHallId
            ? $activeTab.find('.pg_shdl_box[data-hall-id="' + savedHallId + '"]')
            : null;

        if ($targetHall && $targetHall.length) {
            $activeTab.find('.pg_shdl_box').removeClass('active').hide();
            $targetHall.addClass('active').show();

            var hallLinkId = $targetHall.attr('id');
            $activeTab.find('.pgrm_hall_tab a').removeClass('active');
            $activeTab.find('.pgrm_hall_tab a[href="#' + hallLinkId + '"]').addClass('active');

            $('.newsessionBtn').data('dateid', dateId);
            $('.newsessionBtn').data('hallid', savedHallId);
        } else {
            var $firstHall = $activeTab.find('.pg_shdl_box').first();
            var hallId = $firstHall.data('hall-id');
            $('.newsessionBtn').data('dateid', dateId);
            $('.newsessionBtn').data('hallid', hallId);
        }

        // Always persist whatever ended up active, so subsequent AJAX-triggered
        // reloads (sessionRemover/themeRemover/topicRemover) also land here correctly
        localStorage.setItem('schedule_active_dateId', dateId);
        if (savedHallId) {
            localStorage.setItem('schedule_active_hallId', savedHallId);
        }

        // Clean the URL so refreshing the page doesn't keep re-forcing this date/hall
        // forever (optional — remove this if you want the URL to stay "sticky")
        if (urlDateId || urlHallId) {
            var cleanUrl = window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }, 150);
});
</script>

</html>