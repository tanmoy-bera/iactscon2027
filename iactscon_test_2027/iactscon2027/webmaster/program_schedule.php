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
    <?php
        // Store the first date ID and hall ID for the current tab
        $firstDateId = $resultDateListing[0]['id'] ?? '';
        $firstHallId = $resultHall[0]['id'] ?? '';
        ?>

</body>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/manage_additional_data.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/registration.tariff.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/registration.js"></script>
	<script type="text/javascript" language="javascript" src="section_login/scripts/CountryStateRetriver.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/registration.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/dinner_registration.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/accompany_registration.js"></script>
    <script>
    var jsBASE_URL	= "<?=_BASE_URL_?>";
    var jsWemaster_BASE_URL	= "<?=$cfg['SECTION_BASE_URL']?>";
    var CFG = { BASE_URL : "<?=_BASE_URL_?>" };
         var jsSectionBaseURL	= "<?=$cfg['SECTION_BASE_URL']?>";

        </script>
  <div class="pop_up_wrap">
                    <div class="pop_up_inner">
        <div class="pop_up_body "  id="newsession">
            <style>
                /* Select2 selected box */
                #newsession .select2-container--default .select2-selection--single {
                    background: transparent  !important;
                    color: #fff !important;
                    border: 1px solid #2e2e2e !important;
                    height: 36px;
                }
                .select2-search--dropdown .select2-search__field {
                    padding: 4px;
                    width: 100%;
                    box-sizing: border-box;
                    background: #212121c4 !important;
                    color: currentcolor;
                }

                /* Selected text */
                #newsession .select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: #fff !important;
                    line-height: 36px;
                    font-family: 'Poppins';
                      font-size: 12px;
                }

                /* Dropdown arrow */
                #newsession .select2-container--default .select2-selection__arrow b {
                    border-top-color: #fff !important;
                }

                /* Dropdown panel */
                #newsession .select2-dropdown {
                    background: transparent   !important;
                    border: 1px solid #2e2e2e !important;
                }

                /* Search input inside Select2 */
                #newsession .select2-search__field {
                    background: transparent   !important;
                    color: #fff !important;
                    border: 1px solid #2e2e2e !important;
                }

                /* Dropdown options */
                #newsession .select2-results__option {
                    background: transparent   !important;
                    color: #fff !important;
                }

                /* Hovered option */
                #newsession .select2-results__option--highlighted {
                    background: #333 !important;
                    color: #fff !important;
                }

              
            </style>
             <form name="frmInsertSession" id="frmInsertSessionnew" action="full_program_schedule.process.php" method="post" onSubmit="return validateSessionCompose(this);">
                <input type="hidden" name="act" value="insertSession" />
            <div id="hiddenContainer"></div>
            <?php
            $sqlParticipantTypeListing = array();
            $sqlParticipantTypeListing['QUERY'] = "SELECT * FROM " . _DB_SP_PARTICIPANT_TYPE_ . " WHERE `status` = 'A' ORDER BY type_name";
            $resultParticipantTypeListing = $mycms->sql_select($sqlParticipantTypeListing);
            ?>
            <div style="display:none;">
                <select id="participantTypeOptionsSource">
                    <option value="">Select</option>
                    <?php
                    foreach ($resultParticipantTypeListing as $rowParticipantTypeListing) {
                        ?>
                                <option value="<?= $rowParticipantTypeListing['type_name'] ?>">
                                    <?= $rowParticipantTypeListing['type_name'] ?>
                                </option>
                    <?php } ?>
                </select>
            </div>
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Compose Session </span>
                    <p>
                        <a href="javascript:void(null)"
                            class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box w-100">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <h5 class="registration-pop_body_box_heading">
                                <span style="color: var(--default2);">Session Details</span>
                               <span><label class="toggleswitch">Parallel Timing
                                    <input class="toggleswitch-checkbox timingCheckbox" type="checkbox" name="parallel_timing" Value="N">
                                    <div class="toggleswitch-switch timingtoggleswitch"></div></label>
                                </span>
                            </h5>
                            <div class="form_grid g_3">
                                <div class="frm_grp">
                                    <p class="frm-head">Session Title</p>
                                    <input  type="text" name="session_title" id="session_title">
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">Session Hall</p>
                                     <select name="hall_id" id="hall_id" required>
                                        <option value="">----Select Hall----</option>
                                        <?
                                        $sql = array();
                                        $sql['QUERY'] = "SELECT * FROM " . _DB_MASTER_HALL_ . " WHERE status = 'A'";
                                        $result = $mycms->sql_select($sql);
                                        $Counter = 0;
                                        foreach ($result as $key => $value) {
                                            ?>

                                                    <option value="<?= $value['id'] ?>" ><?= $value['hall_title'] ?></option>
                                                <?
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                               <div class="frm_grp">
                                    <p class="frm-head">Session Date</p>
                                      <select name="session_date" id="session_date" style="width:94%;" required>
                                        <option value="">-- Select Date --</option>
                                        <?php
                                        $sqlSelectDate = array();
                                        $sqlSelectDate['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " 
                                                                        WHERE `status` = 'A'";

                                        $resultDate = $mycms->sql_select($sqlSelectDate);
                                        if ($resultDate) {
                                            foreach ($resultDate as $keyDate => $rowDate) {
                                                ?>
                                                                <option value="<?= $rowDate['id'] ?>" ><?= $rowDate['conf_date'] ?></option>
                                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Time</p>
                                        <input  type="time" name="session_strating_time" id="session_strating_time" required >
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Time</p>
                                        <input type="time" name="session_ending" id="session_ending" required >
                                    </div>
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">Ref Color</p>
                                    <select name="sessioncolor" id="sessioncolor">
                                        <option value="#fff" style="background-color:#fff; color:#c5c1c1; font-weight:bold;">White</option>
                                        <option value="#B0B0FF" style="background-color:#B0B0FF; color:#FFFFFF; font-weight:bold;">Blue</option>
                                        <option value="#FFA042" style="background-color:#FFA042; color:#FFFFFF; font-weight:bold;">Brown</option>
                                        <option value="#84FF84" style="background-color:#84FF84; color:#FFFFFF; font-weight:bold;">Green</option>
                                        <option value="#FF71FF" style="background-color:#FF71FF; color:#FFFFFF; font-weight:bold;">Violet</option>
                                        <option value="#FFBD9D" style="background-color:#FFBD9D; color:#FFFFFF; font-weight:bold;">Orange</option>
                                        <option value="#FFFFA6" style="background-color:#FFFFA6; color:#FF6600; font-weight:bold;">Yellow</option>
                                    </select>
                                </div>
                               <div class="frm_grp">
                                    <p class="frm-head">Session Type</p>
                                     <?php $sqlSessionListing = array();
                                     $sqlSessionListing['QUERY'] = "SELECT * FROM " . _DB_SESSION_CLASSIFICATION .
                                         " WHERE status = 'A'";
                                     $resultSessionListing = $mycms->sql_select($sqlSessionListing);
                                     ?>
                                        <select name="session_classifications_id" id="session_classifications_id" required>
                                            <option value="">----Select Type----</option>
                                            <?php foreach ($resultSessionListing as $key => $rowSessionListing) { ?>
                                                        <option value="<?= $rowSessionListing['session_classifications'] ?>"><?= $rowSessionListing['session_classifications'] ?></option>
                                            <?php } ?>
                                        
                                        </select>
                                </div>
                              
                                <div class="accm_add_wrap span_3" id="participantContainer">
                                   <h5 class="registration-pop_body_box_heading">
                                            <span style="color: var(--default2);">Faculty Details</span>
                                             <a href="#" id="addParticipant"  class="add mi-1"><?php add() ?>Add Faculty</a>
                                    </h5>
                                    <div class="accm_add_box bg-transparent py-0 pl-0 participant-row">
                                        <div class="form_grid">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Faculty Name</p>
                                                <select name="participant_id_session[]" class="faculty-select" style="width:100%"></select>          
                                                <input type="hidden" name="participant_name_session[]" class="participant-name">
                                                 <div class="faculty-dropdown"></div>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Nature of Participant</p>
                                                <select name="participant_type_session[]">
                                                     <option value="">
                                                            Select
                                                        </option>
                                                    <?php
                                                    foreach ($resultParticipantTypeListing as $rowParticipantTypeListing) {
                                                        ?>
                                                                <option value="<?= $rowParticipantTypeListing['type_name'] ?>">
                                                                    <?= $rowParticipantTypeListing['type_name'] ?>
                                                                </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <a href="#" class="participant-delete accm_delet icon_hover badge_danger action-transparent">
                                             <? delete() ?> </a>
                                        
                                        
                                        </div>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <h5 class="registration-pop_body_box_heading"><span>Add Group </span><label
                                    class="toggleswitch">
                                    <input type="hidden" class="noThemeHidden" name="noTheme" value="Y">
                                    <input class="toggleswitch-checkbox noGroupcheckbox" type="checkbox" Value="Y">
                                    <div class="toggleswitch-switch noGroupToggle"></div>
                                </label></h5>
                        </div>
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0 group_div">
                            <div class="accm_add_wrap">
                                <div class="accm_add_box pl-0 pr-0 added_grp">
                                    <div class="form_grid g_6" style="padding: 0 var(--gap);">
                                        <h5 class="registration-pop_body_box_heading span_6 mb-0 added_grp_expands"><span
                                                style="color: var(--info2);">Group 1</span><span><?php down() ?></span>
                                        </h5>
                                        <div class="frm_grp span_3">
                                            <p class="frm-head">Group Title</p>
                                            <input placeholder="Group Name" name="schedule_theme_title[]">
                                        </div>
                                        <div class="form_grid span_2">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Start Time</p>
                                                <input type="time" name="theme_start[]" >
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">End Time</p>
                                                <input type="time" name="theme_end[]" >
                                            </div>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Ref Color</p>
                                            <select name="color[]" >
                                                <option value="#DBDBDB" style="background-color:#DBDBDB; color:#000000; font-weight:bold;">Gray</option>
                                                <option value="#c7c7ff" style="background-color:#c7c7ff; color:#000000; font-weight:bold;">Blue</option>
                                                <option value="#ffb367" style="background-color:#ffb367; color:#000000; font-weight:bold;">Brown</option>
                                                <option value="#9cff9c" style="background-color:#9cff9c; color:#000000; font-weight:bold;">Green</option>
                                                <option value="#ff8dff" style="background-color:#ff8dff; color:#000000; font-weight:bold;">Violet</option>
                                                <option value="#ffcab0" style="background-color:#ffcab0; color:#000000; font-weight:bold;">Orange</option>
                                                <option value="#ffffb7" style="background-color:#ffffb7; color:#000000; font-weight:bold;">Yellow</option>
                                            </select>
                                        </div>
                                        <div class="accm_add_wrap span_6 participantContainerGroup" >
                                             <h5 class="registration-pop_body_box_heading">
                                               <span style="color: var(--default2);">Faculty Details</span>
                                               <a href="#"  id="addParticipantGroup" class="add mi-1 addParticipantGroup"><?php add() ?>Add Group Faculty</a>
                                            </h5>
                                            <div class="accm_add_box bg-transparent py-0 pl-0 participant-row-group">
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Faculty Name</p>
                                                        <select name="schedule_theme_participant_id[]" class="faculty-select" style="width:100%"></select>
                                                        <input type="hidden" name="schedule_theme_participant_name[]" class="participant-name-group">

                                                    </div>
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Nature of Participant</p>
                                                        <select name="schedule_theme_participant_as[]">
                                                             <option value="">
                                                            Select
                                                        </option>
                                                            <?php
                                                            foreach ($resultParticipantTypeListing as $rowParticipantTypeListing) {
                                                                ?>
                                                                        <option value="<?= $rowParticipantTypeListing['type_name'] ?>">
                                                                            <?= $rowParticipantTypeListing['type_name'] ?>
                                                                        </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                      <a href="#" class="participant-delete-group accm_delet icon_hover badge_danger action-transparent">
                                                    <? delete() ?></a>
                                        
                                                </div>
                                            </div>
    
                                        </div>
                                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0 span_6">
                                            <h5 class="registration-pop_body_box_heading mt-3"><span>Talks /
                                                    Sub-Sessions</span></h5>
                                             <div class="accm_add_wrap">
                                                <h6 class="accm_add_empty">No talks added yet.</h6>
                                                <div class="accm_add_wrap pl-0 pr-0" class="talkContainer">
                                                
                                                </div>
                                            
                                                <div class="accm_add_box pl-0 pr-0">
                                                    <div class="form_grid g_5" style="padding: 0 var(--gap);">
                                                         <div class="frm_grp span_5">
                                                            <p class="frm-head">Topic Title</p>
                                                            <input placeholder="Topic"  class="topic_title">
                                                        </div>
                                                        <div class="form_grid span_2 g_3">
                                                            <div class="frm_grp span_1">
                                                                <p class="frm-head">Sequence</p>
                                                                <input placeholder="01"  class="Sequence">
                                                            </div>
                                                            <div class="frm_grp span_1">
                                                                <p class="frm-head">Duration</p>
                                                                <input placeholder="15"  class="topic_duration">
                                                            </div>
                                                            <div class="frm_grp span_1">
                                                                <p class="frm-head">Set As</p>
                                                                <div class="cus_check_wrap">
                                                                    <label class="cus_check gender_check">
                                                                        <input type="radio" class="Permanent" name="is_permanent" value="Y">
                                                                        <span class="checkmark">Permanent</span>
                                                                    </label>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="frm_grp span_3">
                                                                <p class="frm-head">Content</p>
                                                                <input placeholder="Content" class="Content">
                                                            </div>
                                                       
                                                        
                                                        <div class="accm_add_wrap span_5" class="participantContainerTopic">
                                                            <h5 class="registration-pop_body_box_heading">
                                                                <span style="color: var(--default2);">Faculty Details</span>
                                                                <a href="#"   class="add mi-1 addParticipanttopicTopic"><?php add() ?>Add Topic Faculty</a>
                                                            </h5>
                                                            <div class="accm_add_box bg-transparent py-0 pl-0 participant-row-topic">
                                                                <div class="form_grid">
                                                                    <div class="frm_grp span_2">
                                                                        <p class="frm-head">Faculty Name</p>
                                                                        <select name="participant_name_topic[]" class="faculty-select" style="width:100%"></select>
                                                                        <div class="faculty-dropdown"></div>
                                                                    </div>

                                                                    <div class="frm_grp span_2">
                                                                        <p class="frm-head">Nature of Participant</p>
                                                                        <select name="participant_type_topic[]">
                                                                            <option value="">
                                                                            Select
                                                                        </option>
                                                                            <?php
                                                                            foreach ($resultParticipantTypeListing as $rowParticipantTypeListing) {
                                                                                ?>
                                                                                        <option value="<?= $rowParticipantTypeListing['type_name'] ?>">
                                                                                            <?= $rowParticipantTypeListing['type_name'] ?>
                                                                                        </option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                    <a href="#" class="participant-delete-topic accm_delet icon_hover badge_danger action-transparent">
                                                                      <? delete() ?></a>
                                                                
                                                                
                                                                </div>
                                                            
                                                            </div>
                                                        </div>
                                                
                                                        <div class="form_grid span_5 g_5">
                                                            
                                                            <div class="frm_grp span_2">
                                                                <p class="frm-head">Tag</p>
                                                                <input placeholder="6+2 min" class="Tag" >
                                                            </div>
                                                            <div class="frm_grp span_2">
                                                                <p class="frm-head">Ref Color</p>
                                                                <select class="topicolor">
                                                                    <option value="#fff" style="background-color:#fff; color:#c5c1c1; font-weight:bold;">White</option>
                                                                    <option value="#B0B0FF" style="background-color:#B0B0FF; color:#FFFFFF; font-weight:bold;">Blue</option>
                                                                    <option value="#FFA042" style="background-color:#FFA042; color:#FFFFFF; font-weight:bold;">Brown</option>
                                                                    <option value="#84FF84" style="background-color:#84FF84; color:#FFFFFF; font-weight:bold;">Green</option>
                                                                    <option value="#FF71FF" style="background-color:#FF71FF; color:#FFFFFF; font-weight:bold;">Violet</option>
                                                                    <option value="#FFBD9D" style="background-color:#FFBD9D; color:#FFFFFF; font-weight:bold;">Orange</option>
                                                                    <option value="#FFFFA6" style="background-color:#FFFFA6; color:#FF6600; font-weight:bold;">Yellow</option>
                                                                </select>
                                                            </div>

                                                            <div class="frm_grp span_1">
                                                                <a href="#" class="topic_add badge_success" class="saveTopic">Save</a>
                                                            </div>
                                                        </div>
                                                      
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 <h5 class="registration-pop_body_box_heading"><span></span><a class="add mi-1"
                                    data-tab="addschedulegroups"><?php add() ?>Add More
                                    Group</a></h5>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0 topic_div">
                            <h5 class="registration-pop_body_box_heading"><span>Talks / Sub-Sessions</span></h5>
                            <div class="accm_add_wrap">
                                <h6 class="accm_add_empty">No talks added yet.</h6>
                                <div class="accm_add_wrap pl-0 pr-0" class="talkContainer">
                                  
                                </div>
                               
                                 <div class="accm_add_box pl-0 pr-0">
                                    <div class="form_grid g_5" style="padding: 0 var(--gap);">
                                        <div class="frm_grp span_5">
                                            <p class="frm-head">Topic Title</p>
                                            <input placeholder="Topic"  class="topic_title">
                                        </div>
                                        <div class="form_grid span_2 g_3">
                                            <div class="frm_grp span_1">
                                                <p class="frm-head">Sequence</p>
                                                <input placeholder="01"  class="Sequence">
                                            </div>
                                            <div class="frm_grp span_1">
                                                <p class="frm-head">Duration</p>
                                                <input placeholder="15"  class="topic_duration">
                                            </div>
                                            <div class="frm_grp span_1">
                                                <p class="frm-head">Set As</p>
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" class="Permanent" name="is_permanent" value="Y">
                                                        <span class="checkmark">Permanent</span>
                                                    </label>

                                                </div>
                                            </div>
                                        </div>
                                         <div class="frm_grp span_3">
                                            <p class="frm-head">Content</p>
                                            <input placeholder="Content" class="Content">
                                        </div>
                                        
                                        
                                        <div class="accm_add_wrap span_5" class="participantContainerTopic">
                                                <h5 class="registration-pop_body_box_heading">
                                                <span style="color: var(--default2);">Faculty Details</span>
                                                <a href="#"   class="add mi-1 addParticipanttopicTopic"><?php add() ?>Add Topic Faculty</a>
                                            </h5>
                                            <div class="accm_add_box bg-transparent py-0 pl-0 participant-row-topic">
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Faculty Name</p>
                                                        <select name="participant_name_topic[]" class="faculty-select" style="width:100%"></select>
                                                        <div class="faculty-dropdown"></div>
                                                    </div>

                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Nature of Participant</p>
                                                        <select name="participant_type_topic[]">
                                                             <option value="">
                                                            Select
                                                        </option>
                                                            <?php
                                                            foreach ($resultParticipantTypeListing as $rowParticipantTypeListing) {
                                                                ?>
                                                                        <option value="<?= $rowParticipantTypeListing['type_name'] ?>">
                                                                            <?= $rowParticipantTypeListing['type_name'] ?>
                                                                        </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                   <a href="#" class="participant-delete-topic accm_delet icon_hover badge_danger action-transparent">
                                                      <? delete() ?></a>
                                                
                                                
                                                </div>
                                            
                                            </div>
                                        </div>
                                   
                                        <div class="form_grid span_5 g_5">
                                           
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Tag</p>
                                                <input placeholder="6+2 min" class="Tag" >
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Ref Color</p>
                                                <select class="topicolor">
                                                    <option value="#fff" style="background-color:#fff; color:#c5c1c1; font-weight:bold;">White</option>
                                                    <option value="#B0B0FF" style="background-color:#B0B0FF; color:#FFFFFF; font-weight:bold;">Blue</option>
                                                    <option value="#FFA042" style="background-color:#FFA042; color:#FFFFFF; font-weight:bold;">Brown</option>
                                                    <option value="#84FF84" style="background-color:#84FF84; color:#FFFFFF; font-weight:bold;">Green</option>
                                                    <option value="#FF71FF" style="background-color:#FF71FF; color:#FFFFFF; font-weight:bold;">Violet</option>
                                                    <option value="#FFBD9D" style="background-color:#FFBD9D; color:#FFFFFF; font-weight:bold;">Orange</option>
                                                    <option value="#FFFFA6" style="background-color:#FFFFA6; color:#FF6600; font-weight:bold;">Yellow</option>
                                                </select>
                                            </div>

                                            <div class="frm_grp span_1">
                                                <a href="#" class="topic_add badge_success" class="saveTopic">Save</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="registration-pop_footer">
                <div class="registration_btn_wrap">
                    <button class="popup_close badge_dark">Cancel</button>
                    <button type="submit" class="mi-1 badge_success"><?php save(); ?>Compose Session</button>
                </div>
            </div>
            <script>
                // Function to get participant type options from the hidden source
            function getParticipantTypeOptions() {
                var $source = $('#participantTypeOptionsSource');
                if ($source.length > 0) {
                    return $source.html();
                }
                
                // Fallback: try to get from any existing dropdown
                var $anyDropdown = $('select[name="participant_type_session[]"], select[name="schedule_theme_participant_as[]"], select[name="participant_type_topic[]"]').first();
                if ($anyDropdown.length > 0) {
                    return $anyDropdown.html();
                }
                
                // Ultimate fallback
                return `
                    <option value="">Select</option>
                    <option value="Chairperson">Chairperson</option>
                    <option value="Speaker">Speaker</option>
                    <option value="Panelist">Panelist</option>
                    <option value="Panel Moderator">Panel Moderator</option>
                    <option value="Case Presenter">Case Presenter</option>
                    <option value="Debate Moderator">Debate Moderator</option>
                    <option value="Expert">Expert</option>
                    <option value="Orator">Orator</option>
                `;
            }
            $(document).ready(function () {
                // Track if group mode is active
                let isGroupMode = false;
                // Track topic index per group
                let topicIndices = {};
                // Track if parallel timing is allowed
                let isParallelTiming = false;

                // ==========================================
                // DURATION VALIDATION FUNCTIONS
                // ==========================================
                
                // Convert time to minutes
                function convertTimeToMinutes(timeStr) {
                    if (!timeStr) return 0;
                    var parts = timeStr.split(':');
                    return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
                }

                // Get session duration in minutes
                function getSessionDuration() {
                    var startTime = $('#session_strating_time').val();
                    var endTime = $('#session_ending').val();
                    if (!startTime || !endTime) return 0;
                    return convertTimeToMinutes(endTime) - convertTimeToMinutes(startTime);
                }

                // Get all topics for noTheme = Y mode
                function getAllTopicsDuration() {
                    var totalDuration = 0;
                    $('.topic_div .talk-item').each(function() {
                        var durationText = $(this).find('n').text().trim();
                        var match = durationText.match(/(\d+)/);
                        if (match) {
                            totalDuration += parseInt(match[1], 10);
                        }
                    });
                    return totalDuration;
                }

                // Get topics for a specific group
                function getGroupTopicsDuration($group) {
                    var totalDuration = 0;
                    $group.find('.talk-item').each(function() {
                        var durationText = $(this).find('n').text().trim();
                        var match = durationText.match(/(\d+)/);
                        if (match) {
                            totalDuration += parseInt(match[1], 10);
                        }
                    });
                    return totalDuration;
                }

                // Get group duration
                function getGroupDuration($group) {
                    var timeInputs = $group.find('input[type="time"]');
                    var startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                    var endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                    if (!startTime || !endTime) return 0;
                    return convertTimeToMinutes(endTime) - convertTimeToMinutes(startTime);
                }

                // Get all groups duration (cumulative)
                function getAllGroupsDuration() {
                    var totalDuration = 0;
                    $('.added_grp').each(function() {
                        var duration = getGroupDuration($(this));
                        totalDuration += duration;
                    });
                    return totalDuration;
                }

                // Check if topic would exceed (returns true/false, shows alert)
               function checkTopicForNoTheme(topicDuration, isEditing, oldDuration) {
                    var sessionDuration = getSessionDuration();
                    if (sessionDuration <= 0) {
                        alert('⚠️ Please set valid Session Start and End Times first!');
                        return false;
                    }
                    
                    var currentTotal = getAllTopicsDuration();
                    
                    // If editing, subtract old duration to avoid double counting
                    if (isEditing && oldDuration > 0) {
                        currentTotal = currentTotal - oldDuration;
                    }
                    
                    var newTotal = currentTotal + topicDuration;
                    
                    if (newTotal > sessionDuration) {
                        alert('⚠️ Warning: Topic will exceed session duration!\n\n' +
                            '📊 Current Topics Total: ' + currentTotal + ' min\n' +
                            '📝 New Topic Duration: ' + topicDuration + ' min\n' +
                            '📈 New Total: ' + newTotal + ' min\n' +
                            '⏰ Session Duration: ' + sessionDuration + ' min\n\n' +
                            '⚠️ The topic will be saved but marked in RED.');
                        return true; // Allow saving
                    }
                    
                    return true; // Allow saving
                }

                // Check if topic would exceed group duration - FIXED for editing
                function checkTopicForGroup($group, topicDuration, isEditing, oldDuration) {
                    var groupDuration = getGroupDuration($group);
                    if (groupDuration <= 0) {
                        alert('⚠️ Please set valid Group Start and End Times first!');
                        return false;
                    }
                    
                    var currentTotal = getGroupTopicsDuration($group);
                    
                    // If editing, subtract old duration to avoid double counting
                    if (isEditing && oldDuration > 0) {
                        currentTotal = currentTotal - oldDuration;
                    }
                    
                    var newTotal = currentTotal + topicDuration;
                    
                    if (newTotal > groupDuration) {
                        var groupTitle = $group.find('input[name="schedule_theme_title[]"]').val() || 'Unnamed Group';
                        alert('⚠️ Warning: Topic will exceed group duration!\n\n' +
                            '📊 Current Topics Total: ' + currentTotal + ' min\n' +
                            '📝 New Topic Duration: ' + topicDuration + ' min\n' +
                            '📈 New Total: ' + newTotal + ' min\n' +
                            '⏰ Group Duration: ' + groupDuration + ' min\n\n' +
                            '⚠️ The topic will be saved but marked in RED.');
                        return true; // Allow saving
                    }
                    
                    return true; // Allow saving
                }

                // Check if group would exceed session duration
                function checkGroupAddition(groupDuration) {
                    var sessionDuration = getSessionDuration();
                    if (sessionDuration <= 0) {
                        alert('⚠️ Please set valid Session Start and End Times first!');
                        return false;
                    }
                    
                    var currentTotal = getAllGroupsDuration();
                    var newTotal = currentTotal + groupDuration;
                    
                    if (newTotal > sessionDuration) {
                        alert('⚠️ Warning: Group will exceed session duration!\n\n' +
                            '📊 Current Groups Total: ' + currentTotal + ' min\n' +
                            // '📝 New Group Duration: ' + groupDuration + ' min\n' +
                            // '📈 New Total: ' + newTotal + ' min\n' +
                            '⏰ Session Duration: ' + sessionDuration + ' min\n\n' +
                            '⚠️ The group will be saved but marked in RED.');
                        return true; // Allow saving
                    }
                    
                    return true; // Allow saving
                }

                // ==========================================
                // MARK RED FOR EXCEEDING TOPICS/GROUPS
                // ==========================================
                function markExceedingTopics() {
                    var noTheme = $('.noThemeHidden').val();
                    
                    if (noTheme === 'Y' || !isGroupMode) {
                        // noTheme = Y - Check topics against session
                        var sessionDuration = getSessionDuration();
                        var cumulativeDuration = 0;
                        
                        $('.topic_div .talk-item').each(function() {
                            var $item = $(this);
                            var durationText = $item.find('n').text().trim();
                            var match = durationText.match(/(\d+)/);
                            
                            if (match) {
                                var duration = parseInt(match[1], 10);
                                cumulativeDuration += duration;
                                
                                // Mark red if cumulative exceeds session
                                if (sessionDuration > 0 && cumulativeDuration > sessionDuration) {
                                    $item.find('n').css('color', 'red');
                                    $item.find('k g span').css('color', 'red');
                                    $item.css('border', '2px solid red');
                                } else {
                                    $item.find('n').css('color', '');
                                    $item.find('k g span').css('color', '');
                                    $item.css('border', '');
                                }
                            }
                        });
              
                    } else {
                        // noTheme = N - Check groups against session, topics against groups
                        var sessionDuration = getSessionDuration();
                        var cumulativeGroupDuration = 0;
                        
                        $('.added_grp').each(function() {
                            var $group = $(this);
                            var groupDuration = getGroupDuration($group);
                            cumulativeGroupDuration += groupDuration;
                            
                            // Mark group red if cumulative exceeds session
                            if (sessionDuration > 0 && cumulativeGroupDuration > sessionDuration) {
                                $group.css('border', '2px solid red');
                                $group.find('.registration-pop_body_box_heading span').css('color', 'red');
                            } else {
                                $group.css('border', '');
                                $group.find('.registration-pop_body_box_heading span').css('color', 'color: var(--info2);');
                            }
                            
                            // Check topics within group
                            var cumulativeTopicDuration = 0;
                            $group.find('.talk-item').each(function() {
                                var $item = $(this);
                                var durationText = $item.find('n').text().trim();
                                var match = durationText.match(/(\d+)/);
                                
                                if (match) {
                                    var duration = parseInt(match[1], 10);
                                    cumulativeTopicDuration += duration;
                                    
                                    // Mark topic red if cumulative exceeds group duration
                                    if (groupDuration > 0 && cumulativeTopicDuration > groupDuration) {
                                        $item.find('n').css('color', 'red');
                                        $item.find('k g span').css('color', 'red');
                                        $item.css('border', '2px solid red');
                                    } else {
                                        $item.find('n').css('color', '');
                                        $item.find('k g span').css('color', '');
                                        $item.css('border', '');
                                    }
                                }
                            });
                        });
                    }
                }

                // ==========================================
                // PARALLEL TIMING TOGGLE HANDLER
                // ==========================================
                $('.timingCheckbox').on('change', function () {
                    isParallelTiming = $(this).is(':checked');
                    $(this).val(isParallelTiming ? 'Y' : 'N');
                    console.log('Parallel Timing:', isParallelTiming ? 'ON (Y)' : 'OFF (N)');
                });

                // ==========================================
                // GROUP TOGGLE HANDLER
                // ==========================================
                $('.noGroupcheckbox').on('change', function () {
                    if ($(this).is(':checked')) {
                        isGroupMode = true;
                        $('.noThemeHidden').val('N');
                        $('.group_div').show();
                        $('.topic_div').hide();
                        
                        setGroupTimeRequired(true);
                        removeGroupFieldNames(true);
                    } else {
                        isGroupMode = false;
                        $('.noThemeHidden').val('Y');
                        $('.group_div').hide();
                        $('.topic_div').show();
                        
                        setGroupTimeRequired(false);
                        removeGroupFieldNames(false);
                        
                        // Remove required attribute from schedule_theme_title fields
                        $('.added_grp').find('input[name="schedule_theme_title[]"]').prop('required', false);
                        $('.added_grp').find('input[name="temp_schedule_theme_title[]"]').prop('required', false);
                    }
                    // Mark exceeding after toggle
                    setTimeout(markExceedingTopics, 300);
                });
                
                // ==========================================
                // FUNCTION TO REMOVE/RESTORE GROUP FIELD NAMES
                // ==========================================
                function removeGroupFieldNames(remove) {
                    if (remove) {
                        // Remove name attributes so fields won't be submitted
                        $('.participant-row-group').each(function() {
                            $(this).find('select[name="schedule_theme_participant_id[]"]').removeAttr('name');
                            $(this).find('input[name="schedule_theme_participant_name[]"]').removeAttr('name');
                            $(this).find('select[name="schedule_theme_participant_as[]"]').removeAttr('name');
                        });
                        
                        $('.added_grp').each(function() {
                            $(this).find('input[name="schedule_theme_title[]"]').removeAttr('name');
                            $(this).find('input[name="theme_start[]"]').removeAttr('name');
                            $(this).find('input[name="theme_end[]"]').removeAttr('name');
                            $(this).find('select[name="color[]"]').removeAttr('name');
                        });
                    } else {
                        // Restore name attributes
                        $('.participant-row-group').each(function() {
                            $(this).find('select:not([name])').attr('name', 'schedule_theme_participant_id[]');
                            $(this).find('input:not([name])').attr('name', 'schedule_theme_participant_name[]');
                            $(this).find('select:not([name])').attr('name', 'schedule_theme_participant_as[]');
                        });
                        
                        $('.added_grp').each(function() {
                            $(this).find('input:not([name])').attr('name', 'schedule_theme_title[]');
                            $(this).find('input:not([name])').attr('name', 'theme_start[]');
                            $(this).find('input:not([name])').attr('name', 'theme_end[]');
                            $(this).find('select:not([name])').attr('name', 'color[]');
                        });
                        
                        // Remove required from schedule_theme_title when group mode is OFF
                        if (!isGroupMode) {
                            $('.added_grp').find('input[name="schedule_theme_title[]"]').prop('required', false);
                        }
                    }
                }
                // ==========================================
                // FUNCTION TO SET GROUP TIME FIELDS REQUIRED
                // ==========================================
                function setGroupTimeRequired(required) {
                    $('.added_grp').each(function() {
                        let $group = $(this);
                        // Restore names temporarily to set required
                        removeGroupFieldNames(false);
                        
                        // Set required for time fields
                        $group.find('input[name="theme_start[]"]').prop('required', required);
                        $group.find('input[name="theme_end[]"]').prop('required', required);
                        
                        // Remove required from schedule_theme_title when group mode is OFF
                        if (!required) {
                            $group.find('input[name="schedule_theme_title[]"]').prop('required', false);
                        }
                        
                        if (isGroupMode) {
                            removeGroupFieldNames(true);
                        }
                    });
                }

                // ==========================================
                // INITIALIZE TOPIC INDICES
                // ==========================================
                function getTopicIndex(groupIndex) {
                    if (!topicIndices[groupIndex]) {
                        topicIndices[groupIndex] = 0;
                    }
                    return topicIndices[groupIndex]++;
                }

                function resetTopicIndices() {
                    topicIndices = {};
                    $('.added_grp').each(function(groupIndex) {
                        let talkCount = $(this).find('.talk-item').length;
                        topicIndices[groupIndex] = talkCount > 0 ? talkCount : 0;
                    });
                    let talkCount = $('.topic_div .talk-item').length;
                    topicIndices[0] = talkCount > 0 ? talkCount : 0;
                }

                // Initial state
                $('.group_div').hide();
                $('.topic_div').show();
                resetTopicIndices();

                // ==========================================
                // TIME CONFLICT VALIDATION FUNCTIONS
                // ==========================================
                function validateGroupTimesFilled() {
                    if (!isGroupMode) {
                        return true;
                    }

                    removeGroupFieldNames(false);
                    
                    let hasEmpty = false;
                    let errorMessage = '';

                    $('.added_grp').each(function(groupIndex) {
                        let $group = $(this);
                        let timeInputs = $group.find('input[type="time"]');
                        let startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                        let endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                        let groupTitle = $group.find('input[type="text"]').filter(function() {
                            return $(this).closest('.participant-row-group').length === 0 && 
                                $(this).closest('.participant-row-topic').length === 0 &&
                                !$(this).hasClass('Sequence') &&
                                !$(this).hasClass('topic_duration') &&
                                !$(this).hasClass('topic_title') &&
                                !$(this).hasClass('Tag') &&
                                !$(this).hasClass('Content');
                        }).first().val() || 'Group ' + (groupIndex + 1);

                        if (!startTime || startTime === '') {
                            hasEmpty = true;
                            errorMessage = '⚠️ Please enter Start Time for "' + groupTitle + '"';
                            return false;
                        }

                        if (!endTime || endTime === '') {
                            hasEmpty = true;
                            errorMessage = '⚠️ Please enter End Time for "' + groupTitle + '"';
                            return false;
                        }

                        if (startTime && endTime) {
                            let startMinutes = convertTimeToMinutes(startTime);
                            let endMinutes = convertTimeToMinutes(endTime);
                            if (endMinutes <= startMinutes) {
                                hasEmpty = true;
                                errorMessage = '⚠️ End Time must be after Start Time for "' + groupTitle + '"';
                                return false;
                            }
                        }
                    });

                    if (isGroupMode) {
                        removeGroupFieldNames(true);
                    }

                    if (hasEmpty) {
                        alert(errorMessage);
                        return false;
                    }

                    return true;
                }

                function validateGroupTimes() {
                    if (isParallelTiming) {
                        return true;
                    }

                    removeGroupFieldNames(false);

                    let groups = [];
                    let hasConflict = false;
                    let errorMessage = '';

                    $('.added_grp').each(function(groupIndex) {
                        let $group = $(this);
                        let timeInputs = $group.find('input[type="time"]');
                        let startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                        let endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                        let groupTitle = $group.find('input[type="text"]').filter(function() {
                            return $(this).closest('.participant-row-group').length === 0 && 
                                $(this).closest('.participant-row-topic').length === 0 &&
                                !$(this).hasClass('Sequence') &&
                                !$(this).hasClass('topic_duration') &&
                                !$(this).hasClass('topic_title') &&
                                !$(this).hasClass('Tag') &&
                                !$(this).hasClass('Content');
                        }).first().val() || 'Group ' + (groupIndex + 1);

                        if (startTime && endTime) {
                            groups.push({
                                index: groupIndex,
                                title: groupTitle,
                                start: startTime,
                                end: endTime,
                                startMinutes: convertTimeToMinutes(startTime),
                                endMinutes: convertTimeToMinutes(endTime)
                            });
                        }
                    });

                    for (let i = 0; i < groups.length; i++) {
                        for (let j = i + 1; j < groups.length; j++) {
                            const g1 = groups[i];
                            const g2 = groups[j];

                            if (g1.startMinutes < g2.endMinutes && g2.startMinutes < g1.endMinutes) {
                                hasConflict = true;
                                errorMessage = '⚠️ Time Conflict!\n\n"' + g1.title + '" (' + g1.start + ' - ' + g1.end + ') overlaps with "' + g2.title + '" (' + g2.start + ' - ' + g2.end + ').\n\nPlease adjust the times or enable Parallel Timing.';
                                break;
                            }
                        }
                        if (hasConflict) break;
                    }

                    if (isGroupMode) {
                        removeGroupFieldNames(true);
                    }

                    if (hasConflict) {
                        alert(errorMessage);
                        return false;
                    }

                    return true;
                }

                // ==========================================
                // COLLECT TOPICS FROM A CONTAINER
                // ==========================================
                function collectTopicsFromContainer($container, groupIndex) {
                    let topicIndex = 0;
                    let talkItems = $container.find('.talk-item');
                    
                    talkItems.each(function() {
                        let $talkItem = $(this);
                        let topicKey = $talkItem.data('topic-key');
                        
                        let $hiddenTopic = $('#hiddenContainer').find('.hidden-topic[data-topic-key="' + topicKey + '"]');
                        
                        if ($hiddenTopic.length > 0) {
                            $hiddenTopic.find('input').each(function() {
                                let $input = $(this);
                                let name = $input.attr('name');
                                let newName = name.replace(/\[(\d+)\]/, '[' + groupIndex + ']');
                                newName = newName.replace(/\]\[(\d+)\]/, '][' + topicIndex + ']');
                                $input.attr('name', newName);
                            });
                            
                            $hiddenTopic.data('group-index', groupIndex);
                            $hiddenTopic.data('topic-index', topicIndex);
                            
                            let oldKey = $hiddenTopic.data('topic-key');
                            let newKey = oldKey.replace(/group_\d+/, 'group_' + groupIndex).replace(/topic_\d+/, 'topic_' + topicIndex);
                            $hiddenTopic.data('topic-key', newKey);
                            $talkItem.data('topic-key', newKey);
                            $talkItem.data('group-index', groupIndex);
                            $talkItem.data('topic-index', topicIndex);
                            
                            topicIndex++;
                        }
                    });
                }

                function updateAllTopicsToIndex0() {
                    let topicIndex = 0;
                    
                    $('#hiddenContainer .hidden-topic').each(function() {
                        let $topic = $(this);
                        
                        $topic.find('input').each(function() {
                            let $input = $(this);
                            let name = $input.attr('name');
                            let newName = name.replace(/\[(\d+)\]/, '[0]');
                            newName = newName.replace(/\]\[(\d+)\]/, '][' + topicIndex + ']');
                            $input.attr('name', newName);
                        });
                        
                        $topic.data('group-index', 0);
                        $topic.data('topic-index', topicIndex);
                        
                        let oldKey = $topic.data('topic-key');
                        let newKey = oldKey.replace(/group_\d+/, 'group_0').replace(/topic_\d+/, 'topic_' + topicIndex);
                        $topic.data('topic-key', newKey);
                        
                        let $talkItem = $('.talk-item[data-topic-key="' + oldKey + '"]');
                        if ($talkItem.length > 0) {
                            $talkItem.data('topic-key', newKey);
                            $talkItem.data('group-index', 0);
                            $talkItem.data('topic-index', topicIndex);
                        }
                        topicIndex++;
                    });
                }

                function updateTopicsForGroup($group, groupIndex) {
                    let topicIndex = 0;
                    let talkItems = $group.find('.talk-item');
                    
                    talkItems.each(function() {
                        let $talkItem = $(this);
                        let topicKey = $talkItem.data('topic-key');
                        
                        let $hiddenTopic = $('#hiddenContainer').find('.hidden-topic[data-topic-key="' + topicKey + '"]');
                        
                        if ($hiddenTopic.length > 0) {
                            $hiddenTopic.find('input').each(function() {
                                let $input = $(this);
                                let name = $input.attr('name');
                                let newName = name.replace(/\[(\d+)\]/, '[' + groupIndex + ']');
                                newName = newName.replace(/\]\[(\d+)\]/, '][' + topicIndex + ']');
                                $input.attr('name', newName);
                            });
                            
                            $hiddenTopic.data('group-index', groupIndex);
                            $hiddenTopic.data('topic-index', topicIndex);
                            
                            let oldKey = $hiddenTopic.data('topic-key');
                            let newKey = oldKey.replace(/group_\d+/, 'group_' + groupIndex).replace(/topic_\d+/, 'topic_' + topicIndex);
                            $hiddenTopic.data('topic-key', newKey);
                            $talkItem.data('topic-key', newKey);
                            $talkItem.data('group-index', groupIndex);
                            $talkItem.data('topic-index', topicIndex);
                            
                            topicIndex++;
                        }
                    });
                }

                // ==========================================
                // COLLECT GROUP PARTICIPANTS
                // ==========================================
                function collectGroupParticipants($group, groupIndex) {
                    let participantCount = 0;
                    
                    $group.find('.participant-row-group').each(function() {
                        let $row = $(this);
                        let facultySelect = $row.find('.faculty-select');
                        let facultyData = facultySelect.select2('data');
                        
                        let participantType = '';
                        participantType = $row.find('select[name="schedule_theme_participant_as[]"]').val();
                        
                        if (!participantType) {
                            let selects = $row.find('select');
                            if (selects.length >= 2) {
                                participantType = selects.eq(1).val();
                            }
                        }
                        
                        if (!participantType) {
                            participantType = $row.find('select:not(.faculty-select)').val();
                        }
                        
                        if (facultyData.length > 0 && facultyData[0].id) {
                            let participantId = facultyData[0].id;
                            let participantNameText = facultyData[0].text;
                            
                            $('#hiddenContainer').append(`
                                <input type="hidden" name="schedule_theme_participant_id[${groupIndex}][${participantCount}]" value="${participantId}">
                                <input type="hidden" name="schedule_theme_participant_name[${groupIndex}][${participantCount}]" value="${escapeHtml(participantNameText)}">
                                <input type="hidden" name="schedule_theme_participant_as[${groupIndex}][${participantCount}]" value="${escapeHtml(participantType || '')}">
                            `);
                            participantCount++;
                        }
                    });
                }

                // Toggle group expand/collapse
                $('.group_div').on('click', '.added_grp_expands', function () {
                    $(this).closest('.added_grp').toggleClass('active');
                    $(this).toggleClass('active');
                });

                // ==========================================
                // ADD MORE GROUP - WITH ALERT BUT ALLOW SAVE
                // ==========================================
                $(document).on('click', '[data-tab="addschedulegroups"]', function (e) {
                    // Get the new group duration from the first empty group
                    var $firstGroup = $('.added_grp:first');
                    var timeInputs = $firstGroup.find('input[type="time"]');
                    var startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                    var endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                    
                    // Check if times are set
                    if (startTime && endTime) {
                        var groupDuration = convertTimeToMinutes(endTime) - convertTimeToMinutes(startTime);
                        if (groupDuration <= 0) {
                            alert('⚠️ End Time must be after Start Time!');
                            e.preventDefault();
                            return false;
                        }
                        
                        // Check group duration (alert but allow)
                        if (isGroupMode && !isParallelTiming) {
                            var sessionDuration = getSessionDuration();
                            if (sessionDuration <= 0) {
                                alert('⚠️ Please set valid Session Start and End Times first!');
                                e.preventDefault();
                                return false;
                            }
                            
                            // Get current groups duration
                            var currentTotal = 0;
                            $('.added_grp').each(function() {
                                var gTimeInputs = $(this).find('input[type="time"]');
                                var gStart = gTimeInputs.length >= 2 ? gTimeInputs.eq(0).val() : '';
                                var gEnd = gTimeInputs.length >= 2 ? gTimeInputs.eq(1).val() : '';
                                if (gStart && gEnd) {
                                    currentTotal += convertTimeToMinutes(gEnd) - convertTimeToMinutes(gStart);
                                }
                            });
                            
                            var newTotal = currentTotal + groupDuration;
                            if (newTotal > sessionDuration) {
                                alert('⚠️ Warning: Group will exceed session duration!\n\n' +
                                    '📊 Current Groups Total: ' + currentTotal + ' min\n' +
                                    // '📝 New Group Duration: ' + groupDuration + ' min\n' +
                                    // '📈 New Total: ' + newTotal + ' min\n' +
                                    '⏰ Session Duration: ' + sessionDuration + ' min\n\n' +
                                    '⚠️ The group will be saved but marked in RED.');
                                // Allow continue
                            }
                        }
                    }
                    
                    removeGroupFieldNames(false);
                    
                    if (!validateGroupTimesFilled()) {
                        e.preventDefault();
                        return false;
                    }

                    if (!validateGroupTimes()) {
                        e.preventDefault();
                        return false;
                    }

                    e.preventDefault();

                    let groupCount = $('.added_grp').length + 1;
                    let $clone = $('.added_grp:first').clone();

                    $clone.find('.registration-pop_body_box_heading span:first')
                        .text('Group ' + groupCount);

                    // Clear all fields
                    $clone.find('input[name="schedule_theme_title[]"]').val('');
                    $clone.find('input[placeholder="Group Name"]').val('');
                    $clone.find('input[type="text"]').filter(function() {
                        return $(this).closest('.participant-row-group').length === 0 && 
                            $(this).closest('.participant-row-topic').length === 0;
                    }).first().val('');
                    $clone.find('input[type="text"]').val('');
                    $clone.find('input[type="time"]').val('');
                    $clone.find('input[type="hidden"]').val('');
                    $clone.find('select').prop('selectedIndex', 0);

                    $clone.find('.Sequence').val('');
                    $clone.find('.topic_duration').val('');
                    $clone.find('.topic_title').val('');
                    $clone.find('.Tag').val('');
                    $clone.find('.Content').val('');
                    $clone.find('.topicolor').prop('selectedIndex', 0);
                    $clone.find('.Permanent').prop('checked', false);

                    $clone.find('.talkContainer').empty();
                    $clone.find('.accm_add_empty').show();

                    $clone.find('.participant-row-topic').not(':first').remove();
                    $clone.find('.participant-row-topic:first').find('select').prop('selectedIndex', 0);
                    $clone.find('.participant-row-topic:first').find('.faculty-select').val(null).trigger('change');

                    $clone.find('.participant-row-group').not(':first').remove();
                    $clone.find('.participant-row-group:first').find('select').prop('selectedIndex', 0);
                    $clone.find('.participant-row-group:first').find('.faculty-select').val(null).trigger('change');

                    $clone.find('.select2').remove();
                    $clone.find('.faculty-select')
                        .removeClass('select2-hidden-accessible')
                        .removeAttr('data-select2-id')
                        .removeAttr('tabindex')
                        .show();

                    topicIndices[groupCount - 1] = 0;

                    if (isGroupMode) {
                        $clone.find('input[name="schedule_theme_title[]"]').val('');
                        $clone.find('input[placeholder="Group Name"]').val('');
                        
                        $clone.find('.participant-row-group').find('select[name="schedule_theme_participant_id[]"]').removeAttr('name');
                        $clone.find('.participant-row-group').find('input[name="schedule_theme_participant_name[]"]').removeAttr('name');
                        $clone.find('.participant-row-group').find('select[name="schedule_theme_participant_as[]"]').removeAttr('name');
                        
                        $clone.find('input[name="schedule_theme_title[]"]').removeAttr('name');
                        $clone.find('input[name="theme_start[]"]').removeAttr('name');
                        $clone.find('input[name="theme_end[]"]').removeAttr('name');
                        $clone.find('select[name="color[]"]').removeAttr('name');
                        
                        $clone.find('input[name="theme_start[]"]').prop('required', true);
                        $clone.find('input[name="theme_end[]"]').prop('required', true);
                        $clone.find('input[name="schedule_theme_title[]"]').prop('required', false);
                    } else {
                        // When group mode is OFF, remove required from schedule_theme_title
                        $clone.find('input[name="schedule_theme_title[]"]').prop('required', false);
                    }

                    $(this).closest('h5').before($clone);

                    let $newGroup = $('.added_grp:last');
                    $newGroup.find('input[name="schedule_theme_title[]"]').val('');
                    $newGroup.find('input[placeholder="Group Name"]').val('');
                    $newGroup.find('input[type="text"]').filter(function() {
                        return $(this).closest('.participant-row-group').length === 0 && 
                            $(this).closest('.participant-row-topic').length === 0;
                    }).first().val('');
                    $newGroup.find('input[type="text"]').val('');

                    $clone.find('.faculty-select').select2({
                        placeholder: "Search faculty...",
                        minimumInputLength: 3,
                        ajax: {
                            url: "full_program_schedule.process.php",
                            type: "POST",
                            dataType: "json",
                            delay: 300,
                            data: function (params) {
                                return {
                                    act: "searchParticipant",
                                    search: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.map(item => ({
                                        id: item.ID,
                                        text: item.NAME
                                    }))
                                };
                            }
                        }
                    });
                    
                    resetTopicIndices();
                    
                    setTimeout(function() {
                        let $finalGroup = $('.added_grp:last');
                        $finalGroup.find('input[name="schedule_theme_title[]"]').val('');
                        $finalGroup.find('input[placeholder="Group Name"]').val('');
                        $finalGroup.find('input[type="text"]').filter(function() {
                            return $(this).closest('.participant-row-group').length === 0 && 
                                $(this).closest('.participant-row-topic').length === 0;
                        }).first().val('');
                    }, 50);
                    
                    // Mark exceeding after adding group
                    setTimeout(markExceedingTopics, 200);
                });

                // ==========================================
                // TOPIC SAVE FUNCTION - WITH ALERT BUT ALLOW SAVE
                // ==========================================
                // Variable to track which topic is being edited
                let editingTopicKey = null;

                // Edit talk - populate form with topic data
                $(document).on('click', '.edit-talk', function (e) {
                    e.preventDefault();
                    
                    var $talkItem = $(this).closest('.talk-item');
                    var topicKey = $talkItem.data('topic-key');
                    editingTopicKey = topicKey;
                    
                    // Find the hidden topic data
                    var $hiddenTopic = $('#hiddenContainer .hidden-topic[data-topic-key="' + topicKey + '"]');
                    if ($hiddenTopic.length === 0) {
                        // Try to find in DOM
                        $hiddenTopic = $('.hidden-topic[data-topic-key="' + topicKey + '"]');
                    }
                    
                    if ($hiddenTopic.length > 0) {
                        // Get the group container
                        var $group = $talkItem.closest('.added_grp');
                        if ($group.length === 0) {
                            $group = $('.topic_div');
                        }
                        
                        // Populate the form fields with data from hidden topic
                        $hiddenTopic.find('input').each(function() {
                            var $input = $(this);
                            var name = $input.attr('name');
                            var value = $input.val();
                            
                            // Match and fill the corresponding input in the form
                            if (name.indexOf('topic_title') !== -1) {
                                $group.find('.topic_title').val(value);
                            } else if (name.indexOf('topic_contant') !== -1) {
                                $group.find('.Content').val(value);
                            } else if (name.indexOf('topic_duration_min') !== -1) {
                                $group.find('.topic_duration').val(value);
                            } else if (name.indexOf('is_duration_permanent') !== -1) {
                                if (value === 'Y') {
                                    $group.find('.Permanent').prop('checked', true);
                                } else {
                                    $group.find('.Permanent').prop('checked', false);
                                }
                            } else if (name.indexOf('sequence') !== -1) {
                                $group.find('.Sequence').val(value);
                            } else if (name.indexOf('reference_tag') !== -1) {
                                $group.find('.Tag').val(value);
                            } else if (name.indexOf('topicColor') !== -1) {
                                $group.find('.topicolor').val(value);
                            } else if (name.indexOf('topic_theme_participant_id') !== -1) {
                                // Handle participants - we'll rebuild the participant rows
                                // Store participant data to rebuild later
                            }
                        });
                        
                        // Rebuild participant rows for the topic
                        var participantIds = [];
                        var participantNames = [];
                        var participantTypes = [];
                        var participantFaculties = [];
                        
                        $hiddenTopic.find('input[name*="topic_theme_participant_id"]').each(function() {
                            var val = $(this).val();
                            if (val) {
                                participantIds.push(val);
                            }
                        });
                        
                        $hiddenTopic.find('input[name*="topic_participant_name"]').each(function() {
                            var val = $(this).val();
                            if (val) {
                                participantNames.push(val);
                            }
                        });
                        
                        $hiddenTopic.find('input[name*="topic_theme_participant_as"]').each(function() {
                            var val = $(this).val();
                            if (val) {
                                participantTypes.push(val);
                            }
                        });
                        
                        // Clear existing participant rows (keep first)
                        var $participantContainer = $group.find('.participantContainerTopic');
                        if ($participantContainer.length === 0) {
                            $participantContainer = $group.find('.accm_add_wrap.span_5');
                        }
                        
                        $participantContainer.find('.participant-row-topic').not(':first').remove();
                        
                        // If there are participants, populate the first row and add more if needed
                        if (participantIds.length > 0) {
                            var $firstRow = $participantContainer.find('.participant-row-topic:first');
                            
                            // Set first participant
                            $firstRow.find('.faculty-select').val(null).trigger('change');
                            
                            // Add a new option and select it
                            var $select = $firstRow.find('.faculty-select');
                            var option = new Option(participantNames[0] || participantIds[0], participantIds[0], true, true);
                            $select.append(option).trigger('change');
                            $select.val(participantIds[0]).trigger('change');
                            
                            // Set participant type
                            $firstRow.find('select[name="participant_type_topic[]"]').val(participantTypes[0] || '');
                            
                            // Add additional participants
                            for (var i = 1; i < participantIds.length; i++) {
                                var typeOptions =  getParticipantTypeOptions();
                                var facultySelectName = $firstRow.find('.faculty-select').attr('name') || 'participant_name_topic[]';
                                
                                var newRow = `
                                    <div class="accm_add_box bg-transparent py-0 pl-0 participant-row-topic">
                                        <div class="form_grid">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Faculty Name</p>
                                                <select name="${facultySelectName}" class="faculty-select" style="width:100%"></select>
                                                <div class="faculty-dropdown"></div>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Nature of Participant</p>
                                                <select name="participant_type_topic[]">
                                                    ${typeOptions}
                                                </select>
                                            </div>
                                            <a href="#" class="participant-delete-topic accm_delet icon_hover badge_danger action-transparent">
                                                <? delete() ?>
                                            </a>
                                        </div>
                                    </div>`;
                                
                                $participantContainer.append(newRow);
                                
                                var $newRow = $participantContainer.find('.participant-row-topic:last');
                                $newRow.find('.faculty-select').select2({
                                    placeholder: "Search faculty...",
                                    minimumInputLength: 3,
                                    ajax: {
                                        url: "full_program_schedule.process.php",
                                        type: "POST",
                                        dataType: "json",
                                        delay: 300,
                                        data: function (params) {
                                            return {
                                                act: "searchParticipant",
                                                search: params.term
                                            };
                                        },
                                        processResults: function (data) {
                                            return {
                                                results: data.map(function(item) {
                                                    return { id: item.ID, text: item.NAME };
                                                })
                                            };
                                        }
                                    }
                                });
                                
                                // Set participant data
                                var $newSelect = $newRow.find('.faculty-select');
                                var newOption = new Option(participantNames[i] || participantIds[i], participantIds[i], true, true);
                                $newSelect.append(newOption).trigger('change');
                                $newSelect.val(participantIds[i]).trigger('change');
                                $newRow.find('select[name="participant_type_topic[]"]').val(participantTypes[i] || '');
                            }
                        }
                        
                        // Scroll to the form
                        $group.find('.topic_title').focus();
                        
                        // Change the Save button text to "Update"
                        $group.find('.topic_add, .saveTopic').text('Update Topic');
                        
                        // Store reference to the talk item being edited
                        $group.data('editing-talk-item', $talkItem);
                        
                        // Show a visual indicator that we're in edit mode
                        $talkItem.css('opacity', '0.5');
                    }
                });

                // ==========================================
                // MODIFIED SAVE TOPIC - WITH ALERT BUT ALLOW SAVE
                // ==========================================
                $(document).on('click', '.topic_add, .saveTopic', function (e) {
                    e.preventDefault();

                    removeGroupFieldNames(false);

                    var $group = $(this).closest('.added_grp');
                    var groupIndex = 0;
                    var isInGroup = false;
                    
                    if (isGroupMode && $group.length > 0) {
                        groupIndex = $('.added_grp').index($group);
                        isInGroup = true;
                    } else {
                        $group = $('.topic_div');
                        groupIndex = 0;
                        isInGroup = false;
                    }

                    // ==========================================
                    // FIX: Define isEditing and $editingTalkItem HERE - BEFORE duration check
                    // ==========================================
                    var isEditing = editingTopicKey !== null;
                    var $editingTalkItem = $group.data('editing-talk-item');

                    // Get duration
                    var duration = parseInt($group.find('.topic_duration').val(), 10);
                    if (isNaN(duration) || duration <= 0) {
                        alert('⚠️ Please enter a valid Duration (positive number)');
                        return false;
                    }

                    // ==========================================
                    // CHECK DURATION - SHOW ALERT BUT ALLOW SAVE
                    // ==========================================
                    var isValid = true;
                    if (isInGroup) {
                        // Check if this topic would exceed group duration
                        var groupDuration = getGroupDuration($group);
                        if (groupDuration <= 0) {
                            alert('⚠️ Please set valid Group Start and End Times first!');
                            return false;
                        }
                        
                        // Get old duration if editing
                        var oldDuration = 0;
                        if (isEditing && $editingTalkItem && $editingTalkItem.length > 0) {
                            var oldDurationText = $editingTalkItem.find('n').text().trim();
                            var oldMatch = oldDurationText.match(/(\d+)/);
                            if (oldMatch) {
                                oldDuration = parseInt(oldMatch[1], 10);
                            }
                        }
                        
                        var currentTotal = getGroupTopicsDuration($group);
                        // Subtract old duration if editing
                        if (oldDuration > 0) {
                            currentTotal = currentTotal - oldDuration;
                        }
                        var newTotal = currentTotal + duration;
                        
                        if (newTotal > groupDuration) {
                            var groupTitle = $group.find('input[name="schedule_theme_title[]"]').val() || 'Unnamed Group';
                            alert('⚠️ Warning: Topic will exceed group duration!\n\n' +
                                '📊 Current Topics Total: ' + currentTotal + ' min\n' +
                                '📝 New Topic Duration: ' + duration + ' min\n' +
                                '📈 New Total: ' + newTotal + ' min\n' +
                                '⏰ Group Duration: ' + groupDuration + ' min\n\n' +
                                '⚠️ The topic will be saved but marked in RED.');
                            // Allow save
                        }
                    } else {
                        // noTheme = Y - Check against session duration
                        var sessionDuration = getSessionDuration();
                        if (sessionDuration <= 0) {
                            alert('⚠️ Please set valid Session Start and End Times first!');
                            return false;
                        }
                        
                        // Get old duration if editing
                        var oldDuration = 0;
                        if (isEditing && $editingTalkItem && $editingTalkItem.length > 0) {
                            var oldDurationText = $editingTalkItem.find('n').text().trim();
                            var oldMatch = oldDurationText.match(/(\d+)/);
                            if (oldMatch) {
                                oldDuration = parseInt(oldMatch[1], 10);
                            }
                        }
                        
                        var currentTotal = getAllTopicsDuration();
                        // Subtract old duration if editing
                        if (oldDuration > 0) {
                            currentTotal = currentTotal - oldDuration;
                        }
                        var newTotal = currentTotal + duration;
                        
                        if (newTotal > sessionDuration) {
                            alert('⚠️ Warning: Topic will exceed session duration!\n\n' +
                                '📊 Current Topics Total: ' + currentTotal + ' min\n' +
                                '📝 New Topic Duration: ' + duration + ' min\n' +
                                '📈 New Total: ' + newTotal + ' min\n' +
                                '⏰ Session Duration: ' + sessionDuration + ' min\n\n' +
                                '⚠️ The topic will be saved but marked in RED.');
                            // Allow save
                        }
                    }

                    // If editing, remove the old talk item and hidden topic
                    if (isEditing && $editingTalkItem && $editingTalkItem.length > 0) {
                        var oldTopicKey = $editingTalkItem.data('topic-key');
                        $('#hiddenContainer .hidden-topic[data-topic-key="' + oldTopicKey + '"]').remove();
                        $editingTalkItem.remove();
                        // Reset opacity
                        $('.talk-item').css('opacity', '1');
                        $group.data('editing-talk-item', null);
                    }

                    var $topicContainer = null;
                    
                    if (isInGroup) {
                        $topicContainer = $group.find('.talkContainer');
                        
                        if ($topicContainer.length === 0) {
                            var $wrap = $group.find('.accm_add_wrap').filter(function() {
                                return $(this).find('.accm_add_empty').length > 0;
                            });
                            if ($wrap.length > 0) {
                                $topicContainer = $wrap.find('.talkContainer');
                                if ($topicContainer.length === 0) {
                                    $topicContainer = $('<div class="accm_add_wrap pl-0 pr-0 talkContainer"></div>');
                                    $wrap.before($topicContainer);
                                }
                            }
                        }
                        
                        if ($topicContainer.length === 0) {
                            var $wraps = $group.find('.accm_add_wrap');
                            for (var i = 0; i < $wraps.length; i++) {
                                var $wrap = $wraps.eq(i);
                                if ($wrap.find('.talk-item').length > 0) {
                                    $topicContainer = $wrap;
                                    break;
                                }
                            }
                        }
                        
                        if ($topicContainer.length === 0) {
                            $topicContainer = $('<div class="accm_add_wrap pl-0 pr-0 talkContainer"></div>');
                            var $heading = $group.find('.registration-pop_body_box_heading:contains("Talks")');
                            if ($heading.length > 0) {
                                $heading.after($topicContainer);
                            } else {
                                var $firstWrap = $group.find('.accm_add_wrap').first();
                                if ($firstWrap.length > 0) {
                                    $firstWrap.before($topicContainer);
                                } else {
                                    $group.append($topicContainer);
                                }
                            }
                        }
                    } else {
                        $topicContainer = $group.find('.talkContainer');
                        
                        if ($topicContainer.length === 0) {
                            var $wrap = $group.find('.accm_add_wrap').filter(function() {
                                return $(this).find('.accm_add_empty').length > 0;
                            });
                            if ($wrap.length > 0) {
                                $topicContainer = $wrap.find('.talkContainer');
                                if ($topicContainer.length === 0) {
                                    $topicContainer = $('<div class="accm_add_wrap pl-0 pr-0 talkContainer"></div>');
                                    $wrap.before($topicContainer);
                                }
                            }
                        }
                        
                        if ($topicContainer.length === 0) {
                            $topicContainer = $('<div class="accm_add_wrap pl-0 pr-0 talkContainer"></div>');
                            var $formWrap = $group.find('.accm_add_wrap').last();
                            if ($formWrap.length > 0) {
                                $formWrap.before($topicContainer);
                            } else {
                                $group.find('.accm_add_wrap').first().after($topicContainer);
                            }
                        }
                    }
                    
                    if ($topicContainer.length === 0) {
                        $topicContainer = $('<div class="accm_add_wrap pl-0 pr-0 talkContainer"></div>');
                        $group.append($topicContainer);
                    }

                    // Get inputs
                    var $sequence = $group.find('.Sequence');
                    var $duration = $group.find('.topic_duration');
                    var $title = $group.find('.topic_title');
                    var $topicolor = $group.find('.topicolor');
                    var $tag = $group.find('.Tag');
                    var $content = $group.find('.Content');
                    var $permanentRadio = $group.find('.Permanent');

                    var sequence = $sequence.val();
                    var durationInput = $duration.val();
                    var title = $title.val();
                    var topicolor = $topicolor.val();
                    var tag = $tag.val();
                    var content = $content.val();

                    if (sequence === '') {
                        alert('Please enter Sequence');
                        $sequence.focus();
                        return false;
                    }

                    if (durationInput === '') {
                        alert('Please enter Duration');
                        $duration.focus();
                        return false;
                    }

                    if (title === '') {
                        alert('Please enter Topic Title');
                        $title.focus();
                        return false;
                    }

                    var isPermanent = $permanentRadio.is(':checked') ? 'Y' : 'N';
                    var topicIndex = getTopicIndex(groupIndex);

                    // Get participants
                    var participants = [];
                    var $participantContainer = $group.find('.participantContainerTopic');
                    if ($participantContainer.length === 0) {
                        $participantContainer = $group.find('.accm_add_wrap.span_5');
                    }
                    
                    var $participantRows = $participantContainer.find('.participant-row-topic');
                    if ($participantRows.length === 0) {
                        $participantRows = $group.find('.participant-row-topic');
                    }
                    
                    $participantRows.each(function() {
                        var $row = $(this);
                        var facultySelect = $row.find('.faculty-select');
                        var participantType = $row.find('select[name="participant_type_topic[]"]').val();
                        var facultyData = facultySelect.select2('data');
                        var selectVal = facultySelect.val();
                        
                        var participantId = null;
                        var participantName = '';
                        
                        if (facultyData && facultyData.length > 0 && facultyData[0] && facultyData[0].id) {
                            participantId = facultyData[0].id;
                            participantName = facultyData[0].text || '';
                        } else if (selectVal && selectVal !== '') {
                            participantId = selectVal;
                        }
                        
                        if (participantId) {
                            participants.push({
                                id: participantId,
                                name: participantName,
                                participantType: participantType || ''
                            });
                        }
                    });

                    var topicKey = 'topic_' + topicIndex + '_group_' + groupIndex;
                    
                    var hidden = `
                        <div class="hidden-topic" data-topic-key="${topicKey}" data-group-index="${groupIndex}" data-topic-index="${topicIndex}">
                            <input type="hidden" name="topic_title[${groupIndex}][${topicIndex}]" value="${escapeHtml(title)}">
                            <input type="hidden" name="topic_id[${groupIndex}][${topicIndex}]" value="">
                            <input type="hidden" name="topic_contant[${groupIndex}][${topicIndex}]" value="${escapeHtml(content || '')}">
                            <input type="hidden" name="topic_duration_min[${groupIndex}][${topicIndex}]" value="${escapeHtml(durationInput)}">
                            <input type="hidden" name="is_duration_permanent[${groupIndex}][${topicIndex}]" value="${isPermanent}">
                            <input type="hidden" name="sequence[${groupIndex}][${topicIndex}]" value="${escapeHtml(sequence)}">
                            <input type="hidden" name="reference_tag[${groupIndex}][${topicIndex}]" value="${escapeHtml(tag || '')}">
                            <input type="hidden" name="topicColor[${groupIndex}][${topicIndex}]" value="${escapeHtml(topicolor)}">
                            <input type="hidden" name="importFromAbstract[${groupIndex}][${topicIndex}]" value="N">
                    `;

                    if (participants.length > 0) {
                        participants.forEach(function(p, pIdx) {
                            hidden += `
                                <input type="hidden" name="topic_theme_participant_id[${groupIndex}][${topicIndex}][${pIdx}]" value="${p.id}">
                                <input type="hidden" name="topic_theme_participant_as[${groupIndex}][${topicIndex}][${pIdx}]" value="${escapeHtml(p.participantType || '')}">
                                <input type="hidden" name="topic_isFaculty[${groupIndex}][${topicIndex}][${pIdx}]" value="Faculty">
                                <input type="hidden" name="topic_participant_name[${groupIndex}][${topicIndex}][${pIdx}]" value="${escapeHtml(p.name || '')}">
                            `;
                        });
                    } else {
                        hidden += `
                            <input type="hidden" name="topic_theme_participant_id[${groupIndex}][${topicIndex}][0]" value="">
                            <input type="hidden" name="topic_theme_participant_as[${groupIndex}][${topicIndex}][0]" value="">
                            <input type="hidden" name="topic_isFaculty[${groupIndex}][${topicIndex}][0]" value="">
                            <input type="hidden" name="topic_participant_name[${groupIndex}][${topicIndex}][0]" value="">
                        `;
                    }

                    hidden += `</div>`;

                    $('#hiddenContainer').append(hidden);

                    var participantshow = [];
                    $participantRows.each(function () {
                        var facultySelect = $(this).find('.faculty-select');
                        var data = facultySelect.select2('data');
                        if (data && data.length > 0 && data[0] && data[0].id) {
                            participantshow.push({
                                id: data[0].id,
                                name: data[0].text
                            });
                        }
                    });

                    var participantText = participantshow.map(function(p) { return p.name; }).join(', ');
                    
                    $group.find('.accm_add_empty').hide();

                    var html = `
                        <div class="accm_add_box pl-0 pr-0 talk-item" data-sequence="${parseInt(sequence) || 0}" data-topic-key="${topicKey}" data-group-index="${groupIndex}" data-topic-index="${topicIndex}">
                            <li class="pg_shdl_talk_box_li align-items-center">
                                <div class="pg_shdl_talk_box_li_left">
                                    <n class="text-left">${durationInput} m<br>${escapeHtml(tag)}</n>
                                    <k> 
                                        <g><span>${escapeHtml(title)}</span></g>
                                        <j><span>${escapeHtml(participantText)}</span></j>
                                    </k> 
                                </div>
                                <div class="action_div action"> 
                                    <a href="javascript:void(0)" class="icon_hover badge_danger br-5 w-auto action-transparent edit-talk"> <? edit() ?> </a> 
                                    <a href="javascript:void(0)" class="icon_hover badge_danger br-5 w-auto action-transparent delete-talk"> <? delete() ?>  </a> 
                                </div>
                            </li>
                        </div>
                    `;

                    $topicContainer.append(html);

                    // Sort by sequence
                    var items = $group.find('.talk-item').get();
                    if (items.length > 0) {
                        items.sort(function(a, b) {
                            return parseInt($(a).data('sequence')) - parseInt($(b).data('sequence'));
                        });
                        var $parent = $group.find('.talk-item').first().parent();
                        $.each(items, function(idx, item) {
                            $parent.append(item);
                        });
                    }

                    // Clear form fields after save
                    $sequence.val('');
                    $duration.val('');
                    $title.val('');
                    $content.val('');
                    $tag.val('');
                    $topicolor.prop('selectedIndex', 0);
                    $permanentRadio.prop('checked', false);
                    
                    // Reset Save button text
                    $group.find('.topic_add, .saveTopic').text('Save');
                    
                    // Reset editing state
                    editingTopicKey = null;
                    $group.data('editing-talk-item', null);
                    $('.talk-item').css('opacity', '1');
                    
                    if ($participantContainer.length > 0) {
                        $participantContainer.find('.participant-row-topic').not(':first').remove();
                        $participantContainer.find('.participant-row-topic:first').find('select').prop('selectedIndex', 0);
                        $participantContainer.find('.participant-row-topic:first').find('.faculty-select').val(null).trigger('change');
                    }
                    
                    $sequence.focus();
                    
                    // Mark exceeding after saving topic
                    setTimeout(markExceedingTopics, 200);
                });
                // ==========================================
                // PARTICIPANT FUNCTIONS
                // ==========================================
                
                function initFacultySelect() {
                    $('.faculty-select').select2({
                        placeholder: "Search faculty...",
                        minimumInputLength: 3,
                        ajax: {
                            url: "full_program_schedule.process.php",
                            type: "POST",
                            dataType: "json",
                            delay: 300,
                            data: function (params) {
                                return {
                                    act: "searchParticipant",
                                    search: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.map(item => ({
                                        id: item.ID,
                                        text: item.NAME
                                    }))
                                };
                            }
                        }
                    });
                }

                initFacultySelect();

                // Add participant to session
                $('#addParticipant').on('click', function (e) {
                    e.preventDefault();

                    var typeOptions =
                        getParticipantTypeOptions();

                    let newRow = `
                        <div class="accm_add_box bg-transparent py-0 pl-0 participant-row">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Faculty Name</p>
                                    <select name="participant_id_session[]" class="faculty-select" style="width:100%"></select>
                                    <input type="hidden" name="participant_name_session[]" class="participant-name">
                                    <div class="faculty-dropdown"></div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Nature of Participant</p>
                                    <select name="participant_type_session[]">
                                        ${typeOptions}
                                    </select>
                                </div>
                                <a href="#" class="participant-delete accm_delet icon_hover badge_danger action-transparent">
                                    <? delete() ?> 
                                </a>
                            </div>
                        </div>`;

                    $('#participantContainer').append(newRow);
                    initFacultySelect();
                });

                $(document).on('click', '.participant-delete', function (e) {
                    e.preventDefault();
                      if(confirm("Are you sure you want to delete this participant?")){
                        $(this).closest('.participant-row').remove();
                     }
                });

                // Add participant to group
                $(document).on('click', '.addParticipantGroup', function (e) {
                    e.preventDefault();
                    let $group = $(this).closest('.added_grp');
                    
                    let $firstRow = $group.find('.participant-row-group:first');
                    
                    let $typeSelect = $firstRow.find('select').eq(1);
                    var typeOptions = $typeSelect.html();
                    
                    if (!typeOptions || typeOptions === '') {
                        typeOptions = $firstRow.find('select[name="schedule_theme_participant_as[]"]').html();
                    }
                    
                    if (!typeOptions || typeOptions === '') {
                        typeOptions = $firstRow.find('select:not(.faculty-select)').html();
                    }
                    
                    if (!typeOptions || typeOptions === '') {
                        typeOptions = getParticipantTypeOptions();

                    }
                    
                    let newRow = `
                        <div class="accm_add_box bg-transparent py-0 pl-0 participant-row-group">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Faculty Name</p>
                                    <select name="schedule_theme_participant_id[]" class="faculty-select" style="width:100%"></select>
                                    <input type="hidden" name="schedule_theme_participant_name[]" class="participant-name-group">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Nature of Participant</p>
                                    <select name="schedule_theme_participant_as[]">
                                        ${typeOptions}
                                    </select>
                                </div>
                                <a href="#" class="participant-delete-group accm_delet icon_hover badge_danger action-transparent">
                                    <? delete() ?>
                                </a>
                            </div>
                        </div>`;

                    $group.find('.participantContainerGroup').append(newRow);
                    
                    let $newRow = $group.find('.participantContainerGroup .participant-row-group:last');
                    $newRow.find('select, input').prop('disabled', false).prop('readonly', false);
                    $newRow.find('select.faculty-select').prop('disabled', false);
                    $newRow.find('select[name="schedule_theme_participant_as[]"]').prop('disabled', false);
                    $newRow.find('input.participant-name-group').prop('disabled', false);
                    
                    if (isGroupMode) {
                        $newRow.find('select[name="schedule_theme_participant_id[]"]').removeAttr('name');
                        $newRow.find('input[name="schedule_theme_participant_name[]"]').removeAttr('name');
                        $newRow.find('select[name="schedule_theme_participant_as[]"]').removeAttr('name');
                    }
                    
                    $newRow.find('.faculty-select').select2({
                        placeholder: "Search faculty...",
                        minimumInputLength: 3,
                        ajax: {
                            url: "full_program_schedule.process.php",
                            type: "POST",
                            dataType: "json",
                            delay: 300,
                            data: function (params) {
                                return {
                                    act: "searchParticipant",
                                    search: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.map(item => ({
                                        id: item.ID,
                                        text: item.NAME
                                    }))
                                };
                            }
                        }
                    });
                });

                $(document).on('click', '.participant-delete-group', function (e) {
                    e.preventDefault();
                     if(confirm("Are you sure you want to delete this participant?")){
                       $(this).closest('.participant-row-group').remove();
                      }
                });

                // Add participant to topic
                $(document).on('click', '.addParticipanttopicTopic', function (e) {
                    e.preventDefault();
                    
                    let $group = $(this).closest('.added_grp');
                    if ($group.length === 0) {
                        $group = $('.topic_div');
                    }
                    
                    let $participantContainer = $group.find('.participantContainerTopic');
                    if ($participantContainer.length === 0) {
                        $participantContainer = $group.find('.accm_add_wrap').filter(function() {
                            return $(this).hasClass('span_5');
                        });
                    }
                    
                    let $firstRow = $participantContainer.find('.participant-row-topic:first');
                    var typeOptionsTopic =     getParticipantTypeOptions();
                    let facultySelectName = $firstRow.find('.faculty-select').attr('name') || 'participant_name_topic[]';

                    let newRow = `
                        <div class="accm_add_box bg-transparent py-0 pl-0 participant-row-topic">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Faculty Name</p>
                                    <select name="${facultySelectName}" class="faculty-select" style="width:100%"></select>
                                    <div class="faculty-dropdown"></div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Nature of Participant</p>
                                    <select name="participant_type_topic[]">
                                        ${typeOptionsTopic}
                                    </select>
                                </div>
                                <a href="#" class="participant-delete-topic accm_delet icon_hover badge_danger action-transparent">
                                    <? delete() ?>
                                </a>
                            </div>
                        </div>`;

                    $participantContainer.append(newRow);
                    
                    let $newRow = $participantContainer.find('.participant-row-topic:last');
                    $newRow.find('select, input').prop('disabled', false).prop('readonly', false);
                    $newRow.find('select.faculty-select').prop('disabled', false);
                    $newRow.find('select[name="participant_type_topic[]"]').prop('disabled', false);
                    
                    $newRow.find('.faculty-select').select2({
                        placeholder: "Search faculty...",
                        minimumInputLength: 3,
                        ajax: {
                            url: "full_program_schedule.process.php",
                            type: "POST",
                            dataType: "json",
                            delay: 300,
                            data: function (params) {
                                return {
                                    act: "searchParticipant",
                                    search: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.map(item => ({
                                        id: item.ID,
                                        text: item.NAME
                                    }))
                                };
                            }
                        }
                    });
                });

                $(document).on('click', '.participant-delete-topic', function (e) {
                    e.preventDefault();
                     if(confirm("Are you sure you want to delete this participant?")){
                      $(this).closest('.participant-row-topic').remove();
                     }
                });

                // ==========================================
                // VALIDATE ON TIME INPUT CHANGE
                // ==========================================
                $(document).on('change', 'input[type="time"]', function () {
                    if (isGroupMode && !isParallelTiming) {
                        removeGroupFieldNames(false);
                        setTimeout(function() {
                            validateGroupTimes();
                            if (isGroupMode) {
                                removeGroupFieldNames(true);
                            }
                        }, 100);
                    }
                    // Mark exceeding after time change
                    setTimeout(markExceedingTopics, 300);
                });

                // Handle faculty select change
                $(document).on('select2:select', '.faculty-select', function (e) {
                    let selected = e.params.data;

                    if ($(this).closest('.participant-row').length) {
                        $(this)
                            .closest('.participant-row')
                            .find('.participant-name')
                            .val(selected.text);
                    }
                });

                // ==========================================
                // FORM SUBMIT HANDLER
                // ==========================================
                $('#frmInsertSessionnew').on('submit', function (e) {
                    removeGroupFieldNames(false);
                    
                    if (!validateGroupTimesFilled()) {
                        e.preventDefault();
                        return false;
                    }

                    if (!validateGroupTimes()) {
                        e.preventDefault();
                        return false;
                    }

                    $('input[name^="theme_start["]').remove();
                    $('input[name^="theme_end["]').remove();
                    $('input[name^="schedule_theme_title["]').remove();
                    $('input[name^="color["]').remove();
                    
                    $('.timingCheckbox').val(isParallelTiming ? 'Y' : 'N');
                    
                    let noTheme = $('.noThemeHidden').val();
                    
                    if (noTheme === 'Y' || !isGroupMode) {
                        let sessionColor = $('#sessioncolor').val() || '#DBDBDB';
                        
                        $('#hiddenContainer').append(`
                            <input type="hidden" name="theme_start[0]" value="${$('#session_strating_time').val()}">
                            <input type="hidden" name="theme_end[0]" value="${$('#session_ending').val()}">
                            <input type="hidden" name="schedule_theme_title[0]" value="">
                            <input type="hidden" name="color[0]" value="${sessionColor}">
                        `);
                        
                        updateAllTopicsToIndex0();
                        
                    } else {
                        removeGroupFieldNames(false);
                        
                        let groupCount = $('.added_grp').length;
                        
                        if (groupCount === 0) {
                            let sessionColor = $('#sessioncolor').val() || '#DBDBDB';
                            $('#hiddenContainer').append(`
                                <input type="hidden" name="theme_start[0]" value="${$('#session_strating_time').val()}">
                                <input type="hidden" name="theme_end[0]" value="${$('#session_ending').val()}">
                                <input type="hidden" name="schedule_theme_title[0]" value="">
                                <input type="hidden" name="color[0]" value="${sessionColor}">
                            `);
                            
                            updateAllTopicsToIndex0();
                            
                        } else {
                            $('.added_grp').each(function (groupIndex) {
                                let $group = $(this);
                                
                                let groupTitle = $group.find('input[name="schedule_theme_title[]"]').val() || '';
                                if (!groupTitle) {
                                    groupTitle = $group.find('input[type="text"]').filter(function() {
                                        return $(this).closest('.participant-row-group').length === 0 && 
                                            $(this).closest('.participant-row-topic').length === 0 &&
                                            !$(this).hasClass('Sequence') &&
                                            !$(this).hasClass('topic_duration') &&
                                            !$(this).hasClass('topic_title') &&
                                            !$(this).hasClass('Tag') &&
                                            !$(this).hasClass('Content');
                                    }).first().val() || '';
                                }

                                let groupColor = $group.find('select[name="color[]"]').val() || '#DBDBDB';
                                
                                if (!groupColor || groupColor === '#DBDBDB') {
                                    let colorSelect = $group.find('select').filter(function() {
                                        return $(this).closest('.participant-row-group').length === 0 &&
                                            $(this).closest('.participant-row-topic').length === 0 &&
                                            !$(this).hasClass('faculty-select') &&
                                            !$(this).hasClass('topicolor');
                                    }).first();
                                    if (colorSelect.length > 0) {
                                        groupColor = colorSelect.val() || '#DBDBDB';
                                    }
                                }
                                
                                let timeInputs = $group.find('input[type="time"]');
                                let groupStartTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                                let groupEndTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                                
                                if (groupIndex === 0) {
                                    if (!groupStartTime) groupStartTime = $('#session_strating_time').val();
                                    if (!groupEndTime) groupEndTime = $('#session_ending').val();
                                }
                                
                                $('#hiddenContainer').append(`
                                    <input type="hidden" name="theme_start[${groupIndex}]" value="${groupStartTime}">
                                    <input type="hidden" name="theme_end[${groupIndex}]" value="${groupEndTime}">
                                    <input type="hidden" name="schedule_theme_title[${groupIndex}]" value="${groupTitle}">
                                    <input type="hidden" name="color[${groupIndex}]" value="${groupColor}">
                                `);
                                
                                collectGroupParticipants($group, groupIndex);
                                updateTopicsForGroup($group, groupIndex);
                            });
                        }
                        
                        removeGroupFieldNames(true);
                    }
                    
                    if (noTheme === 'N' && isGroupMode) {
                        removeGroupFieldNames(true);
                    }
                    
                    console.log('=== Final Hidden Container Contents ===');
                    $('#hiddenContainer input').each(function() {
                        console.log($(this).attr('name') + ' = ' + $(this).val());
                    });
                    
                     if (!confirm("Are you sure you want to add this session?")) {
                            e.preventDefault();
                            return false;
                        }
                        
                        return true;
                });

                // ==========================================
                // HELPER FUNCTION
                // ==========================================
                function escapeHtml(unsafe) {
                    if (!unsafe) return '';
                    return unsafe
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }
                
                // ==========================================
                // INITIAL MARK EXCEEDING ON LOAD
                // ==========================================
                setTimeout(markExceedingTopics, 500);
            });
            </script>
            </form>
        </div>
   
    <!-- New session pop up -->

        <!-- New session pop up -->
        <div class="pop_up_body " id="editsession" >
            <style>
                /* Select2 selected box */
                .edit_topic_add {
                    height: 37px;
                    background: var(--dark1);
                    display: inline-block;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    border-radius: 5px;
                    margin-top: 21px;
                }
                #editsession .select2-container--default .select2-selection--single {
                    background: transparent !important;
                    color: #fff !important;
                    border: 1px solid #2e2e2e !important;
                    height: 36px;
                }
                .select2-search--dropdown .select2-search__field {
                    padding: 4px;
                    width: 100%;
                    box-sizing: border-box;
                    background: #212121c4 !important;
                    color: currentcolor;
                }

                /* Selected text */
                #editsession .select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: #fff !important;
                    line-height: 36px;
                    font-family: 'Poppins';
                    font-size: 12px;
                }

                /* Dropdown arrow */
                #editsession .select2-container--default .select2-selection__arrow b {
                    border-top-color: #fff !important;
                }

                /* Dropdown panel */
                #editsession .select2-dropdown {
                    background: transparent !important;
                    border: 1px solid #2e2e2e !important;
                }

                /* Search input inside Select2 */
                #editsession .select2-search__field {
                    background: transparent !important;
                    color: #fff !important;
                    border: 1px solid #2e2e2e !important;
                }

                /* Dropdown options */
                #editsession .select2-results__option {
                    background: transparent !important;
                    color: #fff !important;
                }

                /* Hovered option */
                #editsession .select2-results__option--highlighted {
                    background: #333 !important;
                    color: #fff !important;
                }
            </style>

            <?php
            $sessionId = $_POST['sessionIdEdit'];

            // Fetch Session Details
            $sqlSelectSession = array();
            $sqlSelectSession['QUERY'] = "SELECT session.*, session.id AS session_id,
                                                hall.hall_title, hall.id AS hall_id,
                                                venue.id AS venue_id, venue.program_venue,
                                                scheduleDate.conf_date AS session_date,
                                                scheduleDate.id AS date_id,
                                                session.session_start_time,
                                                session.session_end_time,
                                                session.parallel_timing,
                                                session.session_color
                                            FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . " session
                                            INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " scheduleDate
                                                ON session.session_date_id = scheduleDate.id
                                            INNER JOIN " . _DB_MASTER_HALL_ . " hall
                                                ON session.session_hall_id = hall.id
                                            INNER JOIN " . _DB_PROGRAM_SCHEDULE_VENUE_ . " venue
                                                ON hall.hall_venue = venue.id
                                            WHERE session.status = 'A' AND session.id = '" . $sessionId . "'";
            $resultSession = $mycms->sql_select($sqlSelectSession);
            $rowSession = $resultSession[0];

            $sessionStartTime = date('H:i', strtotime($rowSession['session_start_time']));
            $sessionEndTime = date('H:i', strtotime($rowSession['session_end_time']));
            $sessionDate = date('Y-m-d', strtotime($rowSession['session_date']));

            // Fetch Themes/Groups for this session
            $sqlThemes = array();
            $sqlThemes['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . "
                        WHERE schedule_id = '" . $sessionId . "' AND status = 'A' ORDER BY id ASC";
            $resultThemes = $mycms->sql_select($sqlThemes);
            $themes = $resultThemes ?: array();

            $sqlTheme = array();
            $sqlTheme['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . "
                        WHERE schedule_id = '" . $sessionId . "' AND status = 'A' ORDER BY id ASC";
            $resultTheme = $mycms->sql_select($sqlTheme);
            $theme = $resultTheme ?: array();

            // Get all participants
            $sqlParticipants = array();
            $sqlParticipants['QUERY'] = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE status = 'A' ORDER BY participant_full_name";
            $allParticipants = $mycms->sql_select($sqlParticipants);

            // Get participant types
            $sqlTypes = array();
            $sqlTypes['QUERY'] = "SELECT * FROM " . _DB_SP_PARTICIPANT_TYPE_ . " WHERE status = 'A' ORDER BY type_name";
            $participantTypes = $mycms->sql_select($sqlTypes);

            // Get halls
            $sqlHalls = array();
            $sqlHalls['QUERY'] = "SELECT * FROM " . _DB_MASTER_HALL_ . " WHERE status = 'A' ORDER BY hall_title";
            $halls = $mycms->sql_select($sqlHalls);

            // Get session classifications
            $sqlClassifications = array();
            $sqlClassifications['QUERY'] = "SELECT * FROM " . _DB_SESSION_CLASSIFICATION . " WHERE status = 'A' ORDER BY session_classifications";
            $classifications = $mycms->sql_select($sqlClassifications);

            // Get session dates
            $sqlDates = array();
            $sqlDates['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " WHERE status = 'A' ORDER BY conf_date";
            $dates = $mycms->sql_select($sqlDates);
            $firstThemeId = !empty($themes) ? $themes[0]['id'] : '';
            $isNoTheme = (count($themes) > 0 && $themes[0]['noTheme'] == 'N') ? false : true;
            ?>

            <form name="frmEditSession" id="frmEditSession" action="full_program_schedule.process.php" method="post">
                <input type="hidden" name="act" value="updateSession" />
                <input type="hidden" name="session_id" value="<?= $sessionId ?>" />
                <input type="hidden" name="noTheme" class="edit_noThemeHidden" value="<?= $isNoTheme ? 'Y' : 'N' ?>" />
                <div style="display:none;">
                    <select id="editParticipantTypeOptionsSource">
                        <option value="">Select</option>
                        <?php foreach ($participantTypes as $pt) { ?>
                                    <option value="<?= $pt['type_name'] ?>"><?= $pt['type_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div id="editHiddenContainer"></div>

                <div class="registration_pop_up">
                    <div class="registration-pop_heading">
                        <span>Edit Session </span>
                        <p>
                            <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                        </p>
                    </div>
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box w-100">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <h5 class="registration-pop_body_box_heading">
                                    <span style="color: var(--default2);">Session Details</span>
                                    <span><label class="toggleswitch">Parallel Timing
                                        <input class="edit_timingCheckbox toggleswitch-checkbox" type="checkbox" name="parallel_timing" value="<?= ($rowSession['parallel_timing'] == 'Y') ? 'Y' : 'N' ?>" <?= ($rowSession['parallel_timing'] == 'Y') ? 'checked' : '' ?>>
                                        <div class="toggleswitch-switch edit_timingtoggleswitch"></div></label>
                                        <input type="hidden" name="parallel_timing_hidden" id="parallel_timing_hidden" value="<?= ($rowSession['parallel_timing'] == 'Y') ? 'Y' : 'N' ?>">
                                    </span>
                                </h5>
                                <div class="form_grid g_3">
                                    <div class="frm_grp">
                                        <p class="frm-head">Session Title</p>
                                        <input type="text" name="session_title" value="<?= htmlspecialchars($rowSession['session_title']) ?>">
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Session Hall</p>
                                        <select name="hall_id" required>
                                            <option value="">----Select Hall----</option>
                                            <?php foreach ($halls as $hall) { ?>
                                                        <option value="<?= $hall['id'] ?>" <?= ($rowSession['hall_id'] == $hall['id']) ? 'selected' : '' ?>><?= $hall['hall_title'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Session Date</p>
                                        <select name="session_date" style="width:94%;" required>
                                            <option value="">-- Select Date --</option>
                                            <?php foreach ($dates as $date) { ?>
                                                        <option value="<?= $date['id'] ?>" <?= ($rowSession['date_id'] == $date['id']) ? 'selected' : '' ?>><?= $date['conf_date'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Start Time</p>
                                            <input type="time" name="session_strating_time" value="<?= $sessionStartTime ?>" required>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">End Time</p>
                                            <input type="time" name="session_ending" value="<?= $sessionEndTime ?>" required>
                                        </div>
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Ref Color</p>
                                        <select name="sessioncolor" id="edit_sessioncolor">
                                            <option value="#fff" <?= ($rowSession['session_color'] == '#fff') ? 'selected' : '' ?> style="background-color:#fff; color:#c5c1c1; font-weight:bold;">White</option>
                                            <option value="#B0B0FF" <?= ($rowSession['session_color'] == '#B0B0FF') ? 'selected' : '' ?> style="background-color:#B0B0FF; color:#FFFFFF; font-weight:bold;">Blue</option>
                                            <option value="#FFA042" <?= ($rowSession['session_color'] == '#FFA042') ? 'selected' : '' ?> style="background-color:#FFA042; color:#FFFFFF; font-weight:bold;">Brown</option>
                                            <option value="#84FF84" <?= ($rowSession['session_color'] == '#84FF84') ? 'selected' : '' ?> style="background-color:#84FF84; color:#FFFFFF; font-weight:bold;">Green</option>
                                            <option value="#FF71FF" <?= ($rowSession['session_color'] == '#FF71FF') ? 'selected' : '' ?> style="background-color:#FF71FF; color:#FFFFFF; font-weight:bold;">Violet</option>
                                            <option value="#FFBD9D" <?= ($rowSession['session_color'] == '#FFBD9D') ? 'selected' : '' ?> style="background-color:#FFBD9D; color:#FFFFFF; font-weight:bold;">Orange</option>
                                            <option value="#FFFFA6" <?= ($rowSession['session_color'] == '#FFFFA6') ? 'selected' : '' ?> style="background-color:#FFFFA6; color:#FF6600; font-weight:bold;">Yellow</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Session Type</p>
                                        <select name="session_classifications_id" required>
                                            <option value="">----Select Type----</option>
                                            <?php foreach ($classifications as $class) { ?>
                                                        <option value="<?= $class['session_classifications'] ?>" <?= ($rowSession['session_classifications_id'] == $class['session_classifications']) ? 'selected' : '' ?>><?= $class['session_classifications'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="accm_add_wrap span_3 edit_participantContainer" id="edit_participantContainer">
                                    
                                        <h5 class="registration-pop_body_box_heading">
                                                <span style="color: var(--default2);">Faculty Details</span>
                                                <a href="#" class="add mi-1 edit_addParticipant"><?php add() ?>Add Faculty</a>
                                        </h5>
                                        <?php
                                        // Fetch session-level participants (no theme, no topic) - WITH DISTINCT
                                        $sqlSessionParticipants = array();
                                        $sqlSessionParticipants['QUERY'] = "SELECT  ps.participant_id, ps.participant_type, pd.participant_full_name
                                            FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " ps
                                            LEFT JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " pd ON ps.participant_id = pd.id
                                            WHERE ps.session_id = '" . $sessionId . "'
                                            AND (ps.theme_id IS NULL OR ps.theme_id = '')
                                            AND (ps.topic_id IS NULL OR ps.topic_id = '')
                                            ORDER BY ps.participant_id";
                                        $sessionParticipants = $mycms->sql_select($sqlSessionParticipants);
                                        ?>
                                        <?php if (!empty($sessionParticipants)) {
                                            $sessionParticipantCount = 0; ?>
                                                    <?php foreach ($sessionParticipants as $sp) {
                                                        $sessionParticipantCount++;
                                                        ?>
                                                                <div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row">
                                                                    <div class="form_grid">
                                                                        <div class="frm_grp span_2">
                                                                            <p class="frm-head">Faculty Name</p>
                                                                            <select name="participant_id_session[]" class="edit_faculty-select" style="width:100%">
                                                                                <option value="<?= $sp['participant_id'] ?>" selected><?= htmlspecialchars($sp['participant_full_name']) ?></option>
                                                                            </select>
                                                                            <input type="hidden" name="participant_name_session[]" class="edit_participant-name" value="<?= htmlspecialchars($sp['participant_full_name']) ?>">
                                                                        </div>
                                                                        <div class="frm_grp span_2">
                                                                            <p class="frm-head">Nature of Participant</p>
                                                                            <select name="participant_type_session[]">
                                                                                <option value="">Select</option>
                                                                                <?php foreach ($participantTypes as $pt) { ?>
                                                                                            <option value="<?= $pt['type_name'] ?>" <?= ($sp['participant_type'] == $pt['type_name']) ? 'selected' : '' ?>><?= $pt['type_name'] ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                        <!-- <?php if ($sessionParticipantCount == 1) { ?>
                                                            <a href="#" class="edit_addParticipant accm_delet icon_hover badge_success action-transparent"><?php add() ?></a>
                                                        <?php } else { ?>
                                                            <a href="#" class="edit_participant-delete accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                                        <?php } ?>                                                    -->
                                                                        <a href="#" class="edit_participant-delete accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>

                                                                    </div>
                                                                </div>
                                                    <?php } ?>
                                        <?php } else { ?>
                                                    <div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row">
                                                        <div class="form_grid">
                                                            <div class="frm_grp span_2">
                                                                <p class="frm-head">Faculty Name</p>
                                                                <select name="participant_id_session[]" class="edit_faculty-select" style="width:100%"></select>
                                                                <input type="hidden" name="participant_name_session[]" class="edit_participant-name">
                                                            </div>
                                                            <div class="frm_grp span_2">
                                                                <p class="frm-head">Nature of Participant</p>
                                                                <select name="participant_type_session[]">
                                                                    <option value="">Select</option>
                                                                    <?php foreach ($participantTypes as $pt) { ?>
                                                                                <option value="<?= $pt['type_name'] ?>"><?= $pt['type_name'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                                <a href="#" class="edit_participant-delete accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                                        </div>
                                                    </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!-- ========================================================== -->
                            <!-- GROUP MODE TOGGLE -->
                            <!-- ========================================================== -->
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <h5 class="registration-pop_body_box_heading"><span>Add Group </span><label class="toggleswitch">
                                    <input type="hidden" class="edit_noThemeHidden" name="noTheme" value="<?= $isNoTheme ? 'Y' : 'N' ?>">
                                    <input class="edit_noGroupcheckbox toggleswitch-checkbox" type="checkbox" <?= !$isNoTheme ? 'checked' : '' ?>>
                                    <div class="toggleswitch-switch edit_noGroupToggle"></div>
                                </label></h5>
                            </div>

                            <!-- ========================================================== -->
                            <!-- GROUP MODE DIV - Always rendered with at least one empty group -->
                            <!-- ========================================================== -->
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0 edit_group_div" <?= $isNoTheme ? 'style="display:none;"' : '' ?>>
                                <div class="accm_add_wrap">
                                    <?php if (!$isNoTheme && !empty($themes)): ?>
                                                <?php
                                                $groupIndex = 0;
                                                foreach ($themes as $theme) {
                                                    $thisGroupIndex = $groupIndex;
                                                    $groupIndex++;

                                                    // Get theme-level participants - WITH DISTINCT
                                                    $sqlThemeParticipants = array();
                                                    $sqlThemeParticipants['QUERY'] = "SELECT  ps.participant_id, ps.participant_type, pd.participant_full_name
                                                FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " ps
                                                LEFT JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " pd ON ps.participant_id = pd.id
                                                WHERE ps.session_id = '" . $sessionId . "'
                                                AND ps.theme_id = '" . $theme['id'] . "'
                                                AND (ps.topic_id IS NULL OR ps.topic_id = '')
                                                ORDER BY ps.participant_id";
                                                    $themeParticipants = $mycms->sql_select($sqlThemeParticipants);

                                                    // Get topics for this theme
                                                    $sqlTopics = array();
                                                    $sqlTopics['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . "
                                                WHERE schedule_theme_id = '" . $theme['id'] . "' AND status = 'A'
                                                ORDER BY sequence ASC, id ASC";
                                                    $themeTopics = $mycms->sql_select($sqlTopics);
                                                    ?>
                                                        <div class="accm_add_box pl-0 pr-0 edit_added_grp">
                                                            <div class="form_grid g_6" style="padding: 0 var(--gap);">
                                                                <h5 class="registration-pop_body_box_heading span_6 mb-0 edit_added_grp_expands">
                                                                    <span style="color: var(--info2);">Group <?= $groupIndex ?></span>
                                                                    <span><?php down() ?></span>
                                                                </h5>
                                                                <input type="hidden" name="existing_theme_id[]" class="edit_existing_theme_id" value="<?= $theme['id'] ?>">
                                                                <div class="frm_grp span_3">
                                                                    <p class="frm-head">Group Title</p>
                                                                    <input placeholder="Group Name" name="schedule_theme_title[]" value="<?= htmlspecialchars($theme['theme_title']) ?>">
                                                                </div>
                                                                <div class="form_grid span_2">
                                                                    <div class="frm_grp span_2">
                                                                        <p class="frm-head">Start Time</p>
                                                                        <input type="time" name="theme_start[]" value="<?= date('H:i', strtotime($theme['theme_time_start'])) ?>">
                                                                    </div>
                                                                    <div class="frm_grp span_2">
                                                                        <p class="frm-head">End Time</p>
                                                                        <input type="time" name="theme_end[]" value="<?= date('H:i', strtotime($theme['theme_time_end'])) ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="frm_grp span_1">
                                                                    <p class="frm-head">Ref Color</p>
                                                                    <select name="color[]">
                                                                        <option value="#DBDBDB" <?= ($theme['theme_color'] == '#DBDBDB') ? 'selected' : '' ?> style="background-color:#DBDBDB; color:#000000; font-weight:bold;">Gray</option>
                                                                        <option value="#c7c7ff" <?= ($theme['theme_color'] == '#c7c7ff') ? 'selected' : '' ?> style="background-color:#c7c7ff; color:#000000; font-weight:bold;">Blue</option>
                                                                        <option value="#ffb367" <?= ($theme['theme_color'] == '#ffb367') ? 'selected' : '' ?> style="background-color:#ffb367; color:#000000; font-weight:bold;">Brown</option>
                                                                        <option value="#9cff9c" <?= ($theme['theme_color'] == '#9cff9c') ? 'selected' : '' ?> style="background-color:#9cff9c; color:#000000; font-weight:bold;">Green</option>
                                                                        <option value="#ff8dff" <?= ($theme['theme_color'] == '#ff8dff') ? 'selected' : '' ?> style="background-color:#ff8dff; color:#000000; font-weight:bold;">Violet</option>
                                                                        <option value="#ffcab0" <?= ($theme['theme_color'] == '#ffcab0') ? 'selected' : '' ?> style="background-color:#ffcab0; color:#000000; font-weight:bold;">Orange</option>
                                                                        <option value="#ffffb7" <?= ($theme['theme_color'] == '#ffffb7') ? 'selected' : '' ?> style="background-color:#ffffb7; color:#000000; font-weight:bold;">Yellow</option>
                                                                    </select>
                                                                </div>
                                                                <div class="accm_add_wrap span_6 edit_participantContainerGroup">
                                                                    <h5 class="registration-pop_body_box_heading">
                                                                            <span style="color: var(--default2);">Faculty Details</span>
                                                                            <a href="#" class="add mi-1 edit_addParticipantGroup"><?php add() ?>Add Group Faculty</a>
                                                                    </h5>
                                                                    <?php if (!empty($themeParticipants)) {
                                                                        $participantCount = 0;
                                                                        ?>
                                                                                <?php foreach ($themeParticipants as $tp) {
                                                                                    $participantCount++; ?>
                                                                                            <div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-group">
                                                                                                <div class="form_grid">
                                                                                                    <div class="frm_grp span_2">
                                                                                                        <p class="frm-head">Faculty Name</p>
                                                                                                        <select name="schedule_theme_participant_id[]" class="edit_faculty-select" style="width:100%">
                                                                                                            <option value="<?= $tp['participant_id'] ?>" selected><?= htmlspecialchars($tp['participant_full_name']) ?></option>
                                                                                                        </select>
                                                                                                        <input type="hidden" name="schedule_theme_participant_name[]" class="edit_participant-name-group" value="<?= htmlspecialchars($tp['participant_full_name']) ?>">
                                                                                                    </div>
                                                                                                    <div class="frm_grp span_2">
                                                                                                        <p class="frm-head">Nature of Participant</p>
                                                                                                        <select name="schedule_theme_participant_as[]">
                                                                                                            <option value="">Select</option>
                                                                                                            <?php foreach ($participantTypes as $pt) { ?>
                                                                                                                        <option value="<?= $pt['type_name'] ?>" <?= ($tp['participant_type'] == $pt['type_name']) ? 'selected' : '' ?>><?= $pt['type_name'] ?></option>
                                                                                                            <?php } ?>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                <!-- <?php if ($participantCount == 1) { ?>
                                                                        <a href="#" class="edit_addParticipantGroup accm_delet icon_hover badge_success action-transparent"><?php add() ?></a>
                                                                    <?php } else { ?>
                                                                        <a href="#" class="edit_participant-delete-group accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                                                    <?php } ?> -->
                                                                                                    <a href="#" class="edit_participant-delete-group accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>

                                                                                                </div>
                                                                                            </div>
                                                                                <?php } ?>
                                                                    <?php } else { ?>
                                                                                <div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-group">
                                                                                    <div class="form_grid">
                                                                                        <div class="frm_grp span_2">
                                                                                            <p class="frm-head">Faculty Name</p>
                                                                                            <select name="schedule_theme_participant_id[]" class="edit_faculty-select" style="width:100%"></select>
                                                                                            <input type="hidden" name="schedule_theme_participant_name[]" class="edit_participant-name-group">
                                                                                        </div>
                                                                                        <div class="frm_grp span_2">
                                                                                            <p class="frm-head">Nature of Participant</p>
                                                                                            <select name="schedule_theme_participant_as[]">
                                                                                                <option value="">Select</option>
                                                                                                <?php foreach ($participantTypes as $pt) { ?>
                                                                                                            <option value="<?= $pt['type_name'] ?>"><?= $pt['type_name'] ?></option>
                                                                                                <?php } ?>
                                                                                            </select>
                                                                                        </div>
                                                                                        <!-- <a href="#" class="edit_addParticipantGroup accm_delet icon_hover badge_success action-transparent"><?php add() ?></a> -->
                                                                                    </div>
                                                                                </div>
                                                                    <?php } ?>
                                                                </div>

                                                                <div class="registration-pop_body_box_inner p-0 bg-transparent border-0 span_6">
                                                                    <h5 class="registration-pop_body_box_heading mt-3"><span>Talks / Sub-Sessions</span></h5>
                                                                    <div class="accm_add_wrap">
                                                                        <h6 class="edit_accm_add_empty" <?= (count($themeTopics) > 0) ? 'style="display:none;"' : '' ?>>No talks added yet.</h6>
                                                                        <div class="accm_add_wrap pl-0 pr-0 edit_talkContainer">
                                                                            <?php
                                                                            $topicSeq = 0;
                                                                            foreach ($themeTopics as $topic) {
                                                                                $topicKey = $topicSeq;
                                                                                $topicSeq++;

                                                                                // Get topic participants - WITH DISTINCT
                                                                                $sqlTopicParticipants = array();
                                                                                $sqlTopicParticipants['QUERY'] = "SELECT  ps.participant_id, ps.participant_type, pd.participant_full_name
                                                                    FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " ps
                                                                    LEFT JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " pd ON ps.participant_id = pd.id
                                                                    WHERE ps.topic_id = '" . $topic['id'] . "'
                                                                    ORDER BY ps.participant_id";
                                                                                $topicParticipants = $mycms->sql_select($sqlTopicParticipants);
                                                                                $topicParticipantNames = array();
                                                                                foreach ($topicParticipants as $tp) {
                                                                                    $topicParticipantNames[] = $tp['participant_full_name'];
                                                                                }
                                                                                ?>
                                                                                        <div class="accm_add_box pl-0 pr-0 edit_talk-item"
                                                                                            data-sequence="<?= $topic['sequence'] ?>"
                                                                                            data-topic-key="edit_topic_<?= $topicKey ?>_group_<?= $thisGroupIndex ?>"
                                                                                            data-group-index="<?= $thisGroupIndex ?>"
                                                                                            data-topic-index="<?= $topicKey ?>">
                                                                                            <li class="pg_shdl_talk_box_li align-items-center">
                                                                                                <div class="pg_shdl_talk_box_li_left">
                                                                                                    <n class="text-left"><?= $topic['topic_time_duration'] ?> m</n>
                                                                                                    <k>
                                                                                                        <g><span><?= htmlspecialchars($topic['topic_title']) ?></span></g>
                                                                                                        <j><span><?= implode(', ', $topicParticipantNames) ?></span></j>
                                                                                                    </k>
                                                                                                </div>
                                                                                                <div class="action_div action">
                                                                                                    <a href="javascript:void(0)" class="icon_hover badge_danger br-5 w-auto action-transparent edit_Edit-talk"><i class="fal fa-pencil"></i></a>
                                                                                                    <a href="javascript:void(0)" class="icon_hover badge_danger br-5 w-auto action-transparent edit_delete-talk"><?php delete() ?></a>
                                                                                                </div>
                                                                                            </li>
                                                                                        </div>
                                                                                        <div class="edit_hidden-topic"
                                                                                            data-topic-key="edit_topic_<?= $topicKey ?>_group_<?= $thisGroupIndex ?>"
                                                                                            data-group-index="<?= $thisGroupIndex ?>"
                                                                                            data-topic-index="<?= $topicKey ?>">
                                                                                            <input type="hidden" name="topic_id[<?= $thisGroupIndex ?>][]" value="<?= $topic['id'] ?>">
                                                                                            <input type="hidden" name="topic_title[<?= $thisGroupIndex ?>][]" value="<?= htmlspecialchars($topic['topic_title']) ?>">
                                                                                            <input type="hidden" name="topic_contant[<?= $thisGroupIndex ?>][]" value="<?= htmlspecialchars($topic['topic_content']) ?>">
                                                                                            <input type="hidden" name="topic_duration_min[<?= $thisGroupIndex ?>][]" value="<?= $topic['topic_time_duration'] ?>">
                                                                                            <input type="hidden" name="is_duration_permanent[<?= $thisGroupIndex ?>][]" value="<?= $topic['is_duration_permanent'] ?>">
                                                                                            <input type="hidden" name="sequence[<?= $thisGroupIndex ?>][]" value="<?= $topic['sequence'] ?>">
                                                                                            <input type="hidden" name="reference_tag[<?= $thisGroupIndex ?>][]" value="<?= htmlspecialchars($topic['reference_tag']) ?>">
                                                                                            <input type="hidden" name="topicColor[<?= $thisGroupIndex ?>][]" value="<?= $topic['topic_color'] ?>">
                                                                                            <input type="hidden" name="importFromAbstract[<?= $thisGroupIndex ?>][]" value="<?= $topic['topic_abstract_id'] ? 'Y' : 'N' ?>">
                                                                                            <?php if (!empty($topicParticipants)) { ?>
                                                                                                        <?php foreach ($topicParticipants as $tp) { ?>
                                                                                                                    <input type="hidden" name="topic_theme_participant_id[<?= $thisGroupIndex ?>][<?= $topicKey ?>][]" value="<?= $tp['participant_id'] ?>">
                                                                                                                    <input type="hidden" name="topic_participant_name[<?= $thisGroupIndex ?>][<?= $topicKey ?>][]" value="<?= htmlspecialchars($tp['participant_full_name']) ?>">
                                                                                                                    <input type="hidden" name="topic_theme_participant_as[<?= $thisGroupIndex ?>][<?= $topicKey ?>][]" value="<?= $tp['participant_type'] ?>">
                                                                                                                    <input type="hidden" name="topic_isFaculty[<?= $thisGroupIndex ?>][<?= $topicKey ?>][]" value="Faculty">
                                                                                                        <?php } ?>
                                                                                            <?php } else { ?>
                                                                                                        <input type="hidden" name="topic_theme_participant_id[<?= $thisGroupIndex ?>][<?= $topicKey ?>][0]" value="">
                                                                                                        <input type="hidden" name="topic_participant_name[<?= $thisGroupIndex ?>][<?= $topicKey ?>][0]" value="">
                                                                                                        <input type="hidden" name="topic_theme_participant_as[<?= $thisGroupIndex ?>][<?= $topicKey ?>][0]" value="">
                                                                                                        <input type="hidden" name="topic_isFaculty[<?= $thisGroupIndex ?>][<?= $topicKey ?>][0]" value="">
                                                                                            <?php } ?>
                                                                                        </div>
                                                                            <?php } ?>
                                                                        </div>

                                                                        <div class="accm_add_box pl-0 pr-0">
                                                                            <div class="form_grid g_5" style="padding: 0 var(--gap);">
                                                                                <div class="frm_grp span_5">
                                                                                    <p class="frm-head">Topic Title</p>
                                                                                    <input placeholder="Topic" class="edit_topic_title">
                                                                                </div>
                                                                                <div class="form_grid span_2 g_3">
                                                                                    <div class="frm_grp span_1">
                                                                                        <p class="frm-head">Sequence</p>
                                                                                        <input placeholder="01" class="edit_Sequence">
                                                                                    </div>
                                                                                    <div class="frm_grp span_1">
                                                                                        <p class="frm-head">Duration</p>
                                                                                        <input placeholder="15" class="edit_topic_duration">
                                                                                    </div>
                                                                                    <div class="frm_grp span_1">
                                                                                        <p class="frm-head">Set As</p>
                                                                                        <div class="cus_check_wrap">
                                                                                            <label class="cus_check gender_check">
                                                                                                <input type="radio" class="edit_Permanent" name="is_permanent_<?= $thisGroupIndex ?>" value="Y">
                                                                                                <span class="checkmark">Permanent</span>
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="frm_grp span_3">
                                                                                    <p class="frm-head">Content</p>
                                                                                    <input placeholder="Content" class="edit_Content">
                                                                                </div>

                                                                                <div class="accm_add_wrap span_5 edit_participantContainerTopic">
                                                                                    <h5 class="registration-pop_body_box_heading">
                                                                                            <span style="color: var(--default2);">Faculty Details</span>
                                                                                            <a href="#" class="add mi-1 edit_addParticipanttopicTopic"><?php add() ?>Add Topic Faculty</a>
                                                                                    </h5>
                                                                                    <div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-topic">
                                                                                        <div class="form_grid">
                                                                                            <div class="frm_grp span_2">
                                                                                                <p class="frm-head">Faculty Name</p>
                                                                                                <select class="edit_faculty-select" style="width:100%"></select>
                                                                                            </div>
                                                                                            <div class="frm_grp span_2">
                                                                                                <p class="frm-head">Nature of Participant</p>
                                                                                                <select class="edit_topic_participant_type">
                                                                                                    <option value="">Select</option>
                                                                                                    <?php foreach ($participantTypes as $pt) { ?>
                                                                                                                <option value="<?= $pt['type_name'] ?>"><?= $pt['type_name'] ?></option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        <a href="#" class="edit_participant-delete-topic accm_delet icon_hover badge_danger action-transparent"><i class="fal fa-trash-alt"></i></a>                                                                    </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form_grid span_5 g_5">
                                                                                    <div class="frm_grp span_2">
                                                                                        <p class="frm-head">Tag</p>
                                                                                        <input placeholder="6+2 min" class="edit_Tag">
                                                                                    </div>
                                                                                    <div class="frm_grp span_2">
                                                                                        <p class="frm-head">Ref Color</p>
                                                                                        <select class="edit_topicolor">
                                                                                            <option value="#fff" style="background-color:#fff; color:#c5c1c1; font-weight:bold;">White</option>
                                                                                            <option value="#B0B0FF" style="background-color:#B0B0FF; color:#FFFFFF; font-weight:bold;">Blue</option>
                                                                                            <option value="#FFA042" style="background-color:#FFA042; color:#FFFFFF; font-weight:bold;">Brown</option>
                                                                                            <option value="#84FF84" style="background-color:#84FF84; color:#FFFFFF; font-weight:bold;">Green</option>
                                                                                            <option value="#FF71FF" style="background-color:#FF71FF; color:#FFFFFF; font-weight:bold;">Violet</option>
                                                                                            <option value="#FFBD9D" style="background-color:#FFBD9D; color:#FFFFFF; font-weight:bold;">Orange</option>
                                                                                            <option value="#FFFFA6" style="background-color:#FFFFA6; color:#FF6600; font-weight:bold;">Yellow</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="frm_grp span_1">
                                                                                        <a href="#" class="edit_topic_add badge_success">Save</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php } ?>
                                    <?php else: ?>
                                                <!-- ========================================================== -->
                                                <!-- EMPTY GROUP - Always available when group mode is turned ON -->
                                                <!-- ========================================================== -->
                                                <div class="accm_add_box pl-0 pr-0 edit_added_grp">
                                                    <div class="form_grid g_6" style="padding: 0 var(--gap);">
                                                        <h5 class="registration-pop_body_box_heading span_6 mb-0 edit_added_grp_expands">
                                                            <span style="color: var(--info2);">Group 1</span>
                                                            <span><?php down() ?></span>
                                                        </h5>
                                                        <input type="hidden" name="existing_theme_id[]" class="edit_existing_theme_id" value="<?= $firstThemeId ?>">
                                                        <div class="frm_grp span_3">
                                                            <p class="frm-head">Group Title</p>
                                                            <input placeholder="Group Name" name="schedule_theme_title[]" value="">
                                                        </div>
                                                        <div class="form_grid span_2">
                                                            <div class="frm_grp span_2">
                                                                <p class="frm-head">Start Time</p>
                                                                <input type="time" name="theme_start[]" value="">
                                                            </div>
                                                            <div class="frm_grp span_2">
                                                                <p class="frm-head">End Time</p>
                                                                <input type="time" name="theme_end[]" value="">
                                                            </div>
                                                        </div>
                                                        <div class="frm_grp span_1">
                                                            <p class="frm-head">Ref Color</p>
                                                            <select name="color[]">
                                                                <option value="#DBDBDB" style="background-color:#DBDBDB; color:#000000; font-weight:bold;">Gray</option>
                                                                <option value="#c7c7ff" style="background-color:#c7c7ff; color:#000000; font-weight:bold;">Blue</option>
                                                                <option value="#ffb367" style="background-color:#ffb367; color:#000000; font-weight:bold;">Brown</option>
                                                                <option value="#9cff9c" style="background-color:#9cff9c; color:#000000; font-weight:bold;">Green</option>
                                                                <option value="#ff8dff" style="background-color:#ff8dff; color:#000000; font-weight:bold;">Violet</option>
                                                                <option value="#ffcab0" style="background-color:#ffcab0; color:#000000; font-weight:bold;">Orange</option>
                                                                <option value="#ffffb7" style="background-color:#ffffb7; color:#000000; font-weight:bold;">Yellow</option>
                                                            </select>
                                                        </div>
                                                        <div class="accm_add_wrap span_6 edit_participantContainerGroup">
                                                            <h5 class="registration-pop_body_box_heading">
                                                                    <span style="color: var(--default2);">Faculty Details</span>
                                                                    <a href="#" class="add mi-1 edit_addParticipantGroup"><?php add() ?>Add Group Faculty</a>
                                                            </h5>
                                                            <div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-group">
                                                                <div class="form_grid">
                                                                    <div class="frm_grp span_2">
                                                                        <p class="frm-head">Faculty Name</p>
                                                                        <select name="schedule_theme_participant_id[]" class="edit_faculty-select" style="width:100%"></select>
                                                                        <input type="hidden" name="schedule_theme_participant_name[]" class="edit_participant-name-group">
                                                                    </div>
                                                                    <div class="frm_grp span_2">
                                                                        <p class="frm-head">Nature of Participant</p>
                                                                        <select name="schedule_theme_participant_as[]">
                                                                            <option value="">Select</option>
                                                                            <?php foreach ($participantTypes as $pt) { ?>
                                                                                        <option value="<?= $pt['type_name'] ?>"><?= $pt['type_name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                            <a href="#" class="edit_participant-delete-group accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0 span_6">
                                                            <h5 class="registration-pop_body_box_heading mt-3"><span>Talks / Sub-Sessions</span></h5>
                                                            <div class="accm_add_wrap">
                                                                <h6 class="edit_accm_add_empty">No talks added yet.</h6>
                                                                <div class="accm_add_wrap pl-0 pr-0 edit_talkContainer"></div>
                                                                <div class="accm_add_box pl-0 pr-0">
                                                                    <div class="form_grid g_5" style="padding: 0 var(--gap);">
                                                                        <div class="frm_grp span_5">
                                                                            <p class="frm-head">Topic Title</p>
                                                                            <input placeholder="Topic" class="edit_topic_title">
                                                                        </div>
                                                                        <div class="form_grid span_2 g_3">
                                                                            <div class="frm_grp span_1">
                                                                                <p class="frm-head">Sequence</p>
                                                                                <input placeholder="01" class="edit_Sequence">
                                                                            </div>
                                                                            <div class="frm_grp span_1">
                                                                                <p class="frm-head">Duration</p>
                                                                                <input placeholder="15" class="edit_topic_duration">
                                                                            </div>
                                                                            <div class="frm_grp span_1">
                                                                                <p class="frm-head">Set As</p>
                                                                                <div class="cus_check_wrap">
                                                                                    <label class="cus_check gender_check">
                                                                                        <input type="radio" class="edit_Permanent" name="is_permanent_0" value="Y">
                                                                                        <span class="checkmark">Permanent</span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="frm_grp span_3">
                                                                            <p class="frm-head">Content</p>
                                                                            <input placeholder="Content" class="edit_Content">
                                                                        </div>

                                                                        <div class="accm_add_wrap span_5 edit_participantContainerTopic">
                                                                            <h5 class="registration-pop_body_box_heading">
                                                                                    <span style="color: var(--default2);">Faculty Details</span>
                                                                                    <a href="#" class="add mi-1 edit_addParticipanttopicTopic"><?php add() ?>Add Topic Faculty</a>
                                                                            </h5>
                                                                            <div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-topic">
                                                                                <div class="form_grid">
                                                                                    <div class="frm_grp span_2">
                                                                                        <p class="frm-head">Faculty Name</p>
                                                                                        <select class="edit_faculty-select" style="width:100%"></select>
                                                                                    </div>
                                                                                    <div class="frm_grp span_2">
                                                                                        <p class="frm-head">Nature of Participant</p>
                                                                                        <select class="edit_topic_participant_type">
                                                                                            <option value="">Select</option>
                                                                                            <?php foreach ($participantTypes as $pt) { ?>
                                                                                                        <option value="<?= $pt['type_name'] ?>"><?= $pt['type_name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                <a href="#" class="edit_participant-delete-topic accm_delet icon_hover badge_danger action-transparent"><i class="fal fa-trash-alt"></i></a>                                                                    </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form_grid span_5 g_5">
                                                                            <div class="frm_grp span_2">
                                                                                <p class="frm-head">Tag</p>
                                                                                <input placeholder="6+2 min" class="edit_Tag">
                                                                            </div>
                                                                            <div class="frm_grp span_2">
                                                                                <p class="frm-head">Ref Color</p>
                                                                                <select class="edit_topicolor">
                                                                                    <option value="#fff" style="background-color:#fff; color:#c5c1c1; font-weight:bold;">White</option>
                                                                                    <option value="#B0B0FF" style="background-color:#B0B0FF; color:#FFFFFF; font-weight:bold;">Blue</option>
                                                                                    <option value="#FFA042" style="background-color:#FFA042; color:#FFFFFF; font-weight:bold;">Brown</option>
                                                                                    <option value="#84FF84" style="background-color:#84FF84; color:#FFFFFF; font-weight:bold;">Green</option>
                                                                                    <option value="#FF71FF" style="background-color:#FF71FF; color:#FFFFFF; font-weight:bold;">Violet</option>
                                                                                    <option value="#FFBD9D" style="background-color:#FFBD9D; color:#FFFFFF; font-weight:bold;">Orange</option>
                                                                                    <option value="#FFFFA6" style="background-color:#FFFFA6; color:#FF6600; font-weight:bold;">Yellow</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="frm_grp span_1">
                                                                                <a href="#" class="edit_topic_add badge_success">Save</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                    <?php endif; ?>

                                    <h5 class="registration-pop_body_box_heading"><span></span><a class="add mi-1" data-tab="editaddschedulegroups"><?php add() ?>Add More Group</a></h5>
                                </div>
                            </div>

                            <!-- ========================================================== -->
                            <!-- NO-GROUP MODE DIV - Always rendered with topic structure -->
                            <!-- ========================================================== -->
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0 edit_topic_div" <?= $isNoTheme ? '' : 'style="display:none;"' ?>>
                                <h5 class="registration-pop_body_box_heading"><span>Talks / Sub-Sessions</span></h5>
                                <div class="accm_add_wrap">
                                    <?php
                                    // Get ALL topics of this session (from every group) as one flat list
                                    $noThemeTopics = array();
                                    if (!empty($themes)) {
                                        $allThemeIds = array();
                                        foreach ($themes as $t) {
                                            if (!empty($t['id'])) $allThemeIds[] = (int)$t['id'];
                                        }
                                        if (!empty($allThemeIds)) {
                                            $sqlNoThemeTopics = array();
                                            $sqlNoThemeTopics['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . "
                                                WHERE schedule_theme_id IN (" . implode(',', $allThemeIds) . ")
                                                AND status = 'A'
                                                ORDER BY schedule_theme_id ASC, sequence ASC, id ASC";
                                            $noThemeTopics = $mycms->sql_select($sqlNoThemeTopics) ?: array();
                                        }
                                    }

                                    // Topics from several groups can have overlapping sequences (1,2,1,2) -> renumber 1..n
                                    $themeIdsInTopics = array();
                                    foreach ($noThemeTopics as $t) {
                                        $themeIdsInTopics[$t['schedule_theme_id']] = true;
                                    }
                                    $mergedFromGroups = count($themeIdsInTopics) > 1;
                                    ?>
                                    <h6 class="edit_accm_add_empty" <?= (count($noThemeTopics) > 0) ? 'style="display:none;"' : '' ?>>No talks added yet.</h6>
                                    <div class="accm_add_wrap pl-0 pr-0 edit_talkContainer">
                                        <?php
                                        $topicSeq = 0;
                                        foreach ($noThemeTopics as $topic) {
                                            $topicKey = $topicSeq;
                                            $topicSeq++;
                                            $displaySeq = $mergedFromGroups ? $topicSeq : $topic['sequence'];
                                            // Get topic participants - WITH DISTINCT
                                            $sqlTopicParticipants = array();
                                            $sqlTopicParticipants['QUERY'] = "SELECT  ps.participant_id, ps.participant_type, pd.participant_full_name
                                                FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " ps
                                                LEFT JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " pd ON ps.participant_id = pd.id
                                                WHERE ps.topic_id = '" . $topic['id'] . "'
                                                ORDER BY ps.participant_id";
                                            $topicParticipants = $mycms->sql_select($sqlTopicParticipants);
                                            $topicParticipantNames = array();
                                            foreach ($topicParticipants as $tp) {
                                                $topicParticipantNames[] = $tp['participant_full_name'];
                                            }
                                            ?>
                                                    <div class="accm_add_box pl-0 pr-0 edit_talk-item"
                                                         data-sequence="<?= $displaySeq ?>"
                                                        data-topic-key="edit_topic_ng_<?= $topicKey ?>_group_0"
                                                        data-group-index="0"
                                                        data-topic-index="<?= $topicKey ?>">
                                                        <li class="pg_shdl_talk_box_li align-items-center">
                                                            <div class="pg_shdl_talk_box_li_left">
                                                                <n class="text-left"><?= $topic['topic_time_duration'] ?> m</n>
                                                                <k>
                                                                    <g><span><?= htmlspecialchars($topic['topic_title']) ?></span></g>
                                                                    <j><span><?= implode(', ', $topicParticipantNames) ?></span></j>
                                                                </k>
                                                            </div>
                                                            <div class="action_div action">
                                                                <a href="javascript:void(0)" class="icon_hover badge_danger br-5 w-auto action-transparent edit_Edit-talk"><i class="fal fa-pencil"></i></a>
                                                                <a href="javascript:void(0)" class="icon_hover badge_danger br-5 w-auto action-transparent edit_delete-talk"><?php delete() ?></a>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    <div class="edit_hidden-topic"
                                                        data-topic-key="edit_topic_ng_<?= $topicKey ?>_group_0"
                                                        data-group-index="0"
                                                         data-topic-index="<?= $topicKey ?>">
                                                        <input type="hidden" name="topic_id[0][]" value="<?= $topic['id'] ?>">
                                                        <input type="hidden" name="topic_title[0][]" value="<?= htmlspecialchars($topic['topic_title']) ?>">
                                                        <input type="hidden" name="topic_contant[0][]" value="<?= htmlspecialchars($topic['topic_content']) ?>">
                                                        <input type="hidden" name="topic_duration_min[0][]" value="<?= $topic['topic_time_duration'] ?>">
                                                        <input type="hidden" name="is_duration_permanent[0][]" value="<?= $topic['is_duration_permanent'] ?>">
                                                        <input type="hidden" name="sequence[0][]" value="<?= $displaySeq ?>">
                                                        <input type="hidden" name="reference_tag[0][]" value="<?= htmlspecialchars($topic['reference_tag']) ?>">
                                                        <input type="hidden" name="topicColor[0][]" value="<?= $topic['topic_color'] ?>">
                                                        <input type="hidden" name="importFromAbstract[0][]" value="<?= $topic['topic_abstract_id'] ? 'Y' : 'N' ?>">
                                                        <?php if (!empty($topicParticipants)) { ?>
                                                                    <?php foreach ($topicParticipants as $tp) { ?>
                                                                                <input type="hidden" name="topic_theme_participant_id[0][<?= $topicKey ?>][]" value="<?= $tp['participant_id'] ?>">
                                                                                <input type="hidden" name="topic_participant_name[0][<?= $topicKey ?>][]" value="<?= htmlspecialchars($tp['participant_full_name']) ?>">
                                                                                <input type="hidden" name="topic_theme_participant_as[0][<?= $topicKey ?>][]" value="<?= $tp['participant_type'] ?>">
                                                                                <input type="hidden" name="topic_isFaculty[0][<?= $topicKey ?>][]" value="Faculty">
                                                                    <?php } ?>
                                                        <?php } else { ?>
                                                                    <input type="hidden" name="topic_theme_participant_id[0][<?= $topicKey ?>][0]" value="">
                                                                    <input type="hidden" name="topic_participant_name[0][<?= $topicKey ?>][0]" value="">
                                                                    <input type="hidden" name="topic_theme_participant_as[0][<?= $topicKey ?>][0]" value="">
                                                                    <input type="hidden" name="topic_isFaculty[0][<?= $topicKey ?>][0]" value="">
                                                        <?php } ?>
                                                    </div>
                                        <?php } ?>
                                    </div>

                                    <div class="accm_add_box pl-0 pr-0">
                                        <div class="form_grid g_5" style="padding: 0 var(--gap);">
                                            <div class="frm_grp span_5">
                                                <p class="frm-head">Topic Title</p>
                                                <input placeholder="Topic" class="edit_topic_title">
                                            </div>
                                            <div class="form_grid span_2 g_3">
                                                <div class="frm_grp span_1">
                                                    <p class="frm-head">Sequence</p>
                                                    <input placeholder="01" class="edit_Sequence">
                                                </div>
                                                <div class="frm_grp span_1">
                                                    <p class="frm-head">Duration</p>
                                                    <input placeholder="15" class="edit_topic_duration">
                                                </div>
                                                <div class="frm_grp span_1">
                                                    <p class="frm-head">Set As</p>
                                                    <div class="cus_check_wrap">
                                                        <label class="cus_check gender_check">
                                                            <input type="radio" class="edit_Permanent" name="is_permanent_0" value="Y">
                                                            <span class="checkmark">Permanent</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="frm_grp span_3">
                                                <p class="frm-head">Content</p>
                                                <input placeholder="Content" class="edit_Content">
                                            </div>

                                            <div class="accm_add_wrap span_5 edit_participantContainerTopic">
                                                <h5 class="registration-pop_body_box_heading">
                                                        <span style="color: var(--default2);">Faculty Details</span>
                                                        <a href="#" class="add mi-1 edit_addParticipanttopicTopic"><?php add() ?>Add Topic Faculty</a>
                                                </h5>
                                                <div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-topic">
                                                    <div class="form_grid">
                                                        <div class="frm_grp span_2">
                                                            <p class="frm-head">Faculty Name</p>
                                                            <select class="edit_faculty-select" style="width:100%"></select>
                                                        </div>
                                                        <div class="frm_grp span_2">
                                                            <p class="frm-head">Nature of Participant</p>
                                                            <select class="edit_topic_participant_type">
                                                                <option value="">Select</option>
                                                                <?php foreach ($participantTypes as $pt) { ?>
                                                                            <option value="<?= $pt['type_name'] ?>"><?= $pt['type_name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    <a href="#" class="edit_participant-delete-topic accm_delet icon_hover badge_danger action-transparent"><i class="fal fa-trash-alt"></i></a>                                                </div>
                                                </div>
                                            </div>

                                            <div class="form_grid span_5 g_5">
                                                <div class="frm_grp span_2">
                                                    <p class="frm-head">Tag</p>
                                                    <input placeholder="6+2 min" class="edit_Tag">
                                                </div>
                                                <div class="frm_grp span_2">
                                                    <p class="frm-head">Ref Color</p>
                                                    <select class="edit_topicolor">
                                                        <option value="#fff" style="background-color:#fff; color:#c5c1c1; font-weight:bold;">White</option>
                                                        <option value="#B0B0FF" style="background-color:#B0B0FF; color:#FFFFFF; font-weight:bold;">Blue</option>
                                                        <option value="#FFA042" style="background-color:#FFA042; color:#FFFFFF; font-weight:bold;">Brown</option>
                                                        <option value="#84FF84" style="background-color:#84FF84; color:#FFFFFF; font-weight:bold;">Green</option>
                                                        <option value="#FF71FF" style="background-color:#FF71FF; color:#FFFFFF; font-weight:bold;">Violet</option>
                                                        <option value="#FFBD9D" style="background-color:#FFBD9D; color:#FFFFFF; font-weight:bold;">Orange</option>
                                                        <option value="#FFFFA6" style="background-color:#FFFFA6; color:#FF6600; font-weight:bold;">Yellow</option>
                                                    </select>
                                                </div>
                                                <div class="frm_grp span_1">
                                                    <a href="#" class="edit_topic_add badge_success">Save</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button type="button" class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update Session</button>
                    </div>
                </div>
            </form>

            <script>
                // Function to get participant type options from the hidden source
                function editGetParticipantTypeOptions() {
                    var $source = $('#editParticipantTypeOptionsSource');
                    if ($source.length > 0 && $source.html() && $source.html().trim() !== '') {
                        return $source.html();
                    }
                    
                    // Fallback: try to get from any existing dropdown
                    var $anyDropdown = $('#editsession select[name="participant_type_session[]"], #editsession select[name="schedule_theme_participant_as[]"], #editsession .edit_topic_participant_type').first();
                    if ($anyDropdown.length > 0 && $anyDropdown.html() && $anyDropdown.html().trim() !== '') {
                        return $anyDropdown.html();
                    }
                    
                    // Ultimate fallback
                    return `
                        <option value="">Select</option>
                        <option value="Chairperson">Chairperson</option>
                        <option value="Speaker">Speaker</option>
                        <option value="Panelist">Panelist</option>
                        <option value="Panel Moderator">Panel Moderator</option>
                        <option value="Case Presenter">Case Presenter</option>
                        <option value="Debate Moderator">Debate Moderator</option>
                        <option value="Expert">Expert</option>
                        <option value="Orator">Orator</option>
                    `;
                }
                // ==========================================================
                    // ==========================================================
                // PREVENT ENTER KEY FROM SUBMITTING FORM - ONLY BUTTON SUBMITS
                // ==========================================================
                (function() {
                    var form = document.getElementById('frmEditSession');
                    if (form) {
                        form.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter' || e.keyCode === 13) {
                                e.preventDefault();
                                e.stopPropagation();
                                return false;
                            }
                        });
                        
                        var inputs = form.querySelectorAll('input, select, textarea');
                        inputs.forEach(function(input) {
                            input.addEventListener('keydown', function(e) {
                                if (e.key === 'Enter' || e.keyCode === 13) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return false;
                                }
                            });
                        });
                    }
                })();

                (function () {
                    var NS = '.editSessionNS';
                    
                    // Always remove any previously-bound handlers
                    $(document).off(NS);

                    var editIsGroupMode = $('#editsession .edit_noGroupcheckbox').is(':checked');
                    var editIsParallelTiming = $('#editsession .edit_timingCheckbox').is(':checked');

                    // ==========================================================
                    // DURATION VALIDATION FUNCTIONS FOR EDIT
                    // ==========================================================
                    
                    function escapeHtmlEdit(unsafe) {
                        if (!unsafe) return '';
                        return String(unsafe)
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                    }

                    function editConvertTimeToMinutes(timeStr) {
                        if (!timeStr) return 0;
                        var parts = timeStr.split(':');
                        return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
                    }

                    // Get session duration for edit
                    function editGetSessionDuration() {
                        var startTime = $('#editsession input[name="session_strating_time"]').val();
                        var endTime = $('#editsession input[name="session_ending"]').val();
                        if (!startTime || !endTime) return 0;
                        return editConvertTimeToMinutes(endTime) - editConvertTimeToMinutes(startTime);
                    }

                    // Get all topics for noTheme = Y mode (edit)
                    function editGetAllTopicsDuration() {
                        var totalDuration = 0;
                        $('#editsession .edit_topic_div .edit_talk-item').each(function() {
                            var durationText = $(this).find('n').text().trim();
                            var match = durationText.match(/(\d+)/);
                            if (match) {
                                totalDuration += parseInt(match[1], 10);
                            }
                        });
                        return totalDuration;
                    }

                    // Get topics for a specific group (edit)
                    function editGetGroupTopicsDuration($group) {
                        var totalDuration = 0;
                        $group.find('.edit_talk-item').each(function() {
                            var durationText = $(this).find('n').text().trim();
                            var match = durationText.match(/(\d+)/);
                            if (match) {
                                totalDuration += parseInt(match[1], 10);
                            }
                        });
                        return totalDuration;
                    }

                    // Get group duration (edit)
                    function editGetGroupDuration($group) {
                        var timeInputs = $group.find('input[type="time"]');
                        var startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                        var endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                        if (!startTime || !endTime) return 0;
                        return editConvertTimeToMinutes(endTime) - editConvertTimeToMinutes(startTime);
                    }

                    // Get all groups duration (edit)
                    function editGetAllGroupsDuration() {
                        var totalDuration = 0;
                        $('#editsession .edit_added_grp:visible').each(function() {
                            var duration = editGetGroupDuration($(this));
                            totalDuration += duration;
                        });
                        return totalDuration;
                    }

                    // ==========================================================
                    // MARK RED FOR EXCEEDING TOPICS/GROUPS (EDIT)
                    // ==========================================================
                    function editMarkExceedingTopics() {
                        var noTheme = $('#editsession .edit_noThemeHidden').val();
                        
                        if (noTheme === 'Y' || !editIsGroupMode) {
                            // noTheme = Y - Check topics against session
                            var sessionDuration = editGetSessionDuration();
                            var cumulativeDuration = 0;
                            
                            $('#editsession .edit_topic_div .edit_talk-item').each(function() {
                                var $item = $(this);
                                var durationText = $item.find('n').text().trim();
                                var match = durationText.match(/(\d+)/);
                                
                                if (match) {
                                    var duration = parseInt(match[1], 10);
                                    cumulativeDuration += duration;
                                    
                                    if (sessionDuration > 0 && cumulativeDuration > sessionDuration) {
                                        $item.find('n').css('color', 'red');
                                        $item.find('k g span').css('color', 'red');
                                        $item.css('border', '2px solid red');
                                    } else {
                                        $item.find('n').css('color', '');
                                        $item.find('k g span').css('color', '');
                                        $item.css('border', '');
                                    }
                                }
                            });
                            
                        } else {
                            // noTheme = N - Check groups against session, topics against groups
                            var sessionDuration = editGetSessionDuration();
                            var cumulativeGroupDuration = 0;
                            
                            $('#editsession .edit_added_grp:visible').each(function() {
                                var $group = $(this);
                                var groupDuration = editGetGroupDuration($group);
                                cumulativeGroupDuration += groupDuration;
                                
                                // Mark group red if cumulative exceeds session
                                if (sessionDuration > 0 && cumulativeGroupDuration > sessionDuration) {
                                    $group.css('border', '2px solid red');
                                    $group.find('.registration-pop_body_box_heading span').css('color', 'red');
                                } else {
                                    $group.css('border', '');
                                    $group.find('.registration-pop_body_box_heading span').css('color', 'color: var(--info2);');
                                }
                                
                                // Check topics within group
                                var cumulativeTopicDuration = 0;
                                $group.find('.edit_talk-item').each(function() {
                                    var $item = $(this);
                                    var durationText = $item.find('n').text().trim();
                                    var match = durationText.match(/(\d+)/);
                                    
                                    if (match) {
                                        var duration = parseInt(match[1], 10);
                                        cumulativeTopicDuration += duration;
                                        
                                        if (groupDuration > 0 && cumulativeTopicDuration > groupDuration) {
                                            $item.find('n').css('color', 'red');
                                            $item.find('k g span').css('color', 'red');
                                            $item.css('border', '2px solid red');
                                        } else {
                                            $item.find('n').css('color', '');
                                            $item.find('k g span').css('color', '');
                                            $item.css('border', '');
                                        }
                                    }
                                });
                            });
                        }
                    }

                    function editFacultySelectConfig() {
                        return {
                            placeholder: "Search faculty...",
                            minimumInputLength: 3,
                            ajax: {
                                url: "full_program_schedule.process.php",
                                type: "POST",
                                dataType: "json",
                                delay: 300,
                                data: function (params) {
                                    return { act: "searchParticipant", search: params.term };
                                },
                                processResults: function (data) {
                                    return {
                                        results: data.map(function (item) {
                                            return { id: item.ID, text: item.NAME };
                                        })
                                    };
                                }
                            }
                        };
                    }

                    function editInitFacultySelect($scope) {
                        var $targets = $scope ? $scope.find('.edit_faculty-select') : $('#editsession .edit_faculty-select');
                        $targets.each(function () {
                            if (!$(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2(editFacultySelectConfig());
                            }
                        });
                    }

                    function editRemoveGroupFieldNames(remove) {
                        if (remove) {
                            $('#editsession .edit_participant-row-group').each(function () {
                                $(this).find('select[name="schedule_theme_participant_id[]"]').removeAttr('name');
                                $(this).find('input[name="schedule_theme_participant_name[]"]').removeAttr('name');
                                $(this).find('select[name="schedule_theme_participant_as[]"]').removeAttr('name');
                            });
                            $('#editsession .edit_added_grp').each(function () {
                                $(this).find('input[name="schedule_theme_title[]"]').removeAttr('name');
                                $(this).find('input[name="theme_start[]"]').removeAttr('name');
                                $(this).find('input[name="theme_end[]"]').removeAttr('name');
                                $(this).find('select[name="color[]"]').removeAttr('name');
                            });
                        } else {
                            $('#editsession .edit_participant-row-group').each(function () {
                                $(this).find('select:not([name])').attr('name', 'schedule_theme_participant_id[]');
                                $(this).find('input:not([name])').attr('name', 'schedule_theme_participant_name[]');
                                $(this).find('select:not([name])').attr('name', 'schedule_theme_participant_as[]');
                            });
                            $('#editsession .edit_added_grp').each(function () {
                                $(this).find('input:not([name])').attr('name', 'schedule_theme_title[]');
                                $(this).find('input[type="time"]:not([name])').each(function (i) {
                                    $(this).attr('name', i === 0 ? 'theme_start[]' : 'theme_end[]');
                                });
                                $(this).find('select:not([name])').attr('name', 'color[]');
                            });
                        }
                    }

                    // ==========================================================
                    // Toggle between group and no-group mode
                    // ==========================================================
                    function editToggleMode() {
                        var showGroup = $('#editsession .edit_noGroupcheckbox').is(':checked');
                        
                        if (showGroup) {
                            $('#editsession .edit_group_div').show();
                            $('#editsession .edit_topic_div').hide();
                            $('#editsession .edit_noThemeHidden').val('N');
                            $('#editsession .edit_group_div').find('input, select, textarea').prop('disabled', false);
                            $('#editsession .edit_topic_div').find('input, select, textarea').prop('disabled', true);
                        } else {
                            $('#editsession .edit_group_div').hide();
                            $('#editsession .edit_topic_div').show();
                            $('#editsession .edit_noThemeHidden').val('Y');
                            $('#editsession .edit_group_div').find('input, select, textarea').prop('disabled', true);
                            $('#editsession .edit_topic_div').find('input, select, textarea').prop('disabled', false);
                        }
                        // Mark exceeding after toggle
                        setTimeout(editMarkExceedingTopics, 300);
                    }

                    // ----------------------------------------------------
                    // Toggles
                    // ----------------------------------------------------
                    $('#editsession .edit_timingCheckbox').off(NS).on('change' + NS, function () {
                        editIsParallelTiming = $(this).is(':checked');
                        var val = editIsParallelTiming ? 'Y' : 'N';
                        $(this).val(val);
                        $('#parallel_timing_hidden').val(val);
                        // Mark exceeding after parallel timing change
                        setTimeout(editMarkExceedingTopics, 300);
                    });

                    $('#editsession .edit_noGroupcheckbox').off(NS).on('change' + NS, function () {
                        editIsGroupMode = $(this).is(':checked');
                        editToggleMode();
                    });

                    // Call on page load
                    editToggleMode();

                    $('#editsession .edit_group_div').off(NS).on('click' + NS, '.edit_added_grp_expands', function () {
                        $(this).closest('.edit_added_grp').toggleClass('active');
                        $(this).toggleClass('active');
                    });

                    // ----------------------------------------------------
                    // Time validation
                    // ----------------------------------------------------
                    function editValidateGroupTimesFilled() {
                        if (!editIsGroupMode) return true;
                        editRemoveGroupFieldNames(false);
                        var hasEmpty = false, errorMessage = '';

                        $('#editsession .edit_added_grp:visible').each(function (groupIndex) {
                            var $group = $(this);
                            var timeInputs = $group.find('input[type="time"]');
                            var startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                            var endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                            var groupTitle = $group.find('input[name="schedule_theme_title[]"]').val() || ('Group ' + (groupIndex + 1));

                            if (startTime || endTime) {
                                if (!startTime) {
                                    hasEmpty = true;
                                    errorMessage = '⚠️ Please enter Start Time for "' + groupTitle + '"';
                                    return false;
                                }
                                if (!endTime) {
                                    hasEmpty = true;
                                    errorMessage = '⚠️ Please enter End Time for "' + groupTitle + '"';
                                    return false;
                                }
                                if (editConvertTimeToMinutes(endTime) <= editConvertTimeToMinutes(startTime)) {
                                    hasEmpty = true;
                                    errorMessage = '⚠️ End Time must be after Start Time for "' + groupTitle + '"';
                                    return false;
                                }
                            }
                        });

                        if (editIsGroupMode) editRemoveGroupFieldNames(true);
                        if (hasEmpty) { alert(errorMessage); return false; }
                        return true;
                    }

                    function editValidateGroupTimes() {
                        if (editIsParallelTiming) return true;
                        editRemoveGroupFieldNames(false);

                        var groups = [], hasConflict = false, errorMessage = '';

                        $('#editsession .edit_added_grp:visible').each(function (groupIndex) {
                            var $group = $(this);
                            var timeInputs = $group.find('input[type="time"]');
                            var startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                            var endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                            var groupTitle = $group.find('input[name="schedule_theme_title[]"]').val() || ('Group ' + (groupIndex + 1));

                            if (startTime && endTime) {
                                groups.push({
                                    title: groupTitle, start: startTime, end: endTime,
                                    startMinutes: editConvertTimeToMinutes(startTime),
                                    endMinutes: editConvertTimeToMinutes(endTime)
                                });
                            }
                        });

                        for (var i = 0; i < groups.length; i++) {
                            for (var j = i + 1; j < groups.length; j++) {
                                var g1 = groups[i], g2 = groups[j];
                                if (g1.startMinutes < g2.endMinutes && g2.startMinutes < g1.endMinutes) {
                                    hasConflict = true;
                                    errorMessage = '⚠️ Time Conflict!\n\n"' + g1.title + '" (' + g1.start + ' - ' + g1.end + ') overlaps with "' + g2.title + '" (' + g2.start + ' - ' + g2.end + ').\n\nPlease adjust the times or enable Parallel Timing.';
                                    break;
                                }
                            }
                            if (hasConflict) break;
                        }

                        if (editIsGroupMode) editRemoveGroupFieldNames(true);
                        if (hasConflict) { alert(errorMessage); return false; }
                        return true;
                    }

                    $('#editsession').off(NS, 'input[type="time"]').on('change' + NS, 'input[type="time"]', function () {
                        if (editIsGroupMode && !editIsParallelTiming) {
                            editRemoveGroupFieldNames(false);
                            setTimeout(function () {
                                editValidateGroupTimes();
                                if (editIsGroupMode) editRemoveGroupFieldNames(true);
                            }, 100);
                        }
                        // Mark exceeding after time change
                        setTimeout(editMarkExceedingTopics, 300);
                    });

                    // Session time change - mark exceeding
                    $('#editsession').off(NS, 'input[name="session_strating_time"], input[name="session_ending"]').on('change' + NS, 'input[name="session_strating_time"], input[name="session_ending"]', function () {
                        setTimeout(editMarkExceedingTopics, 300);
                    });

                    // ----------------------------------------------------
                    // Session-level participants
                    // ----------------------------------------------------
                    editInitFacultySelect();

                    $('#editsession').off(NS, '.edit_addParticipant').on('click' + NS, '.edit_addParticipant', function (e) {
                        e.preventDefault();
                        
                        var typeOptions = editGetParticipantTypeOptions();

                        var $newRow = $(
                            '<div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row">' +
                                '<div class="form_grid">' +
                                    '<div class="frm_grp span_2">' +
                                        '<p class="frm-head">Faculty Name</p>' +
                                        '<select name="participant_id_session[]" class="edit_faculty-select" style="width:100%"></select>' +
                                        '<input type="hidden" name="participant_name_session[]" class="edit_participant-name">' +
                                    '</div>' +
                                    '<div class="frm_grp span_2">' +
                                        '<p class="frm-head">Nature of Participant</p>' +
                                        '<select name="participant_type_session[]">' + typeOptions + '</select>' +
                                    '</div>' +
                                    '<a href="#" class="edit_participant-delete accm_delet icon_hover badge_danger action-transparent"><i class="fal fa-trash-alt"></i></a>' +
                                '</div>' +
                            '</div>'
                        );

                        $('#edit_participantContainer').append($newRow);
                        editInitFacultySelect($newRow);

                        $newRow.find('.edit_faculty-select').on('select2:select', function (e2) {
                            var selected = e2.params.data;
                            var $row = $(this).closest('.edit_participant-row');
                            
                            var selectedId = selected.id;
                            var isDuplicate = false;
                            $('#edit_participantContainer .edit_participant-row').each(function() {
                                var $rowCheck = $(this);
                                var selectCheck = $rowCheck.find('.edit_faculty-select');
                                if ($rowCheck[0] === $row[0]) return;
                                var val = selectCheck.val();
                                if (val && val === selectedId) {
                                    isDuplicate = true;
                                    return false;
                                }
                            });
                            
                            if (isDuplicate) {
                                alert('This participant is already added!');
                                $(this).val(null).trigger('change');
                                return;
                            }
                            
                            $row.find('.edit_participant-name').val(selected.text);
                        });
                    });

                    $('#editsession').off(NS, '.edit_participant-delete').on('click' + NS, '.edit_participant-delete', function (e) {
                        e.preventDefault();
                        if(confirm("Are you sure you want to delete this participant?")){
                        $(this).closest('.edit_participant-row').remove();
                        }
                    });

                    // ----------------------------------------------------
                    // Group-level participants
                    // ----------------------------------------------------
                    $('#editsession').off(NS, '.edit_addParticipantGroup').on('click' + NS, '.edit_addParticipantGroup', function (e) {
                        e.preventDefault();
                        var $group = $(this).closest('.edit_added_grp');
                        var $firstRow = $group.find('.edit_participant-row-group:first');
                        var typeOptions = editGetParticipantTypeOptions();
                        var $newRow = $(
                            '<div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-group">' +
                                '<div class="form_grid">' +
                                    '<div class="frm_grp span_2">' +
                                        '<p class="frm-head">Faculty Name</p>' +
                                        '<select name="schedule_theme_participant_id[]" class="edit_faculty-select" style="width:100%"></select>' +
                                        '<input type="hidden" name="schedule_theme_participant_name[]" class="edit_participant-name-group">' +
                                    '</div>' +
                                    '<div class="frm_grp span_2">' +
                                        '<p class="frm-head">Nature of Participant</p>' +
                                        '<select name="schedule_theme_participant_as[]">' + typeOptions + '</select>' +
                                    '</div>' +
                                    '<a href="#" class="edit_participant-delete-group accm_delet icon_hover badge_danger action-transparent"><i class="fal fa-trash-alt"></i></a>' +
                                '</div>' +
                            '</div>'
                        );

                        $group.find('.edit_participantContainerGroup').append($newRow);

                        if (editIsGroupMode) {
                            $newRow.find('select[name="schedule_theme_participant_id[]"]').removeAttr('name');
                            $newRow.find('input[name="schedule_theme_participant_name[]"]').removeAttr('name');
                            $newRow.find('select[name="schedule_theme_participant_as[]"]').removeAttr('name');
                        }

                        editInitFacultySelect($newRow);
                        $newRow.find('.edit_faculty-select').on('select2:select', function (e2) {
                            var selected = e2.params.data;
                            $(this).closest('.edit_participant-row-group').find('.edit_participant-name-group').val(selected.text);
                        });
                    });

                    $('#editsession').off(NS, '.edit_participant-delete-group').on('click' + NS, '.edit_participant-delete-group', function (e) {
                        e.preventDefault();
                    if(confirm("Are you sure you want to delete this participant?")){

                        $(this).closest('.edit_participant-row-group').remove();
                    }
                    });

                    // ----------------------------------------------------
                    // Topic-level participants
                    // ----------------------------------------------------
                    $('#editsession').off(NS, '.edit_addParticipanttopicTopic').on('click' + NS, '.edit_addParticipanttopicTopic', function (e) {
                        e.preventDefault();
                        var $group = $(this).closest('.edit_added_grp');
                        if ($group.length === 0) $group = $('#editsession .edit_topic_div');

                        var $participantContainer = $group.find('.edit_participantContainerTopic');
                        var $firstRow = $participantContainer.find('.edit_participant-row-topic:first');
                        var typeOptionsTopic = editGetParticipantTypeOptions();

                        var $newRow = $(
                            '<div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-topic">' +
                                '<div class="form_grid">' +
                                    '<div class="frm_grp span_2">' +
                                        '<p class="frm-head">Faculty Name</p>' +
                                        '<select class="edit_faculty-select" style="width:100%"></select>' +
                                    '</div>' +
                                    '<div class="frm_grp span_2">' +
                                        '<p class="frm-head">Nature of Participant</p>' +
                                        '<select class="edit_topic_participant_type">' + typeOptionsTopic + '</select>' +
                                    '</div>' +
                                    '<a href="#" class="edit_participant-delete-topic accm_delet icon_hover badge_danger action-transparent"><i class="fal fa-trash-alt"></i></a>' +
                                '</div>' +
                            '</div>'
                        );

                        $participantContainer.append($newRow);
                        editInitFacultySelect($newRow);
                    });

                    $('#editsession').off(NS, '.edit_participant-delete-topic').on('click' + NS, '.edit_participant-delete-topic', function (e) {
                        e.preventDefault();
                    if(confirm("Are you sure you want to delete this participant?")){

                        $(this).closest('.edit_participant-row-topic').remove();
                        }
                    });

                    // ----------------------------------------------------
                    // Add More Group
                    // ----------------------------------------------------
                    $('#editsession').off(NS, '[data-tab="editaddschedulegroups"]').on('click' + NS, '[data-tab="editaddschedulegroups"]', function (e) {
                        e.preventDefault();
                        editRemoveGroupFieldNames(false);

                        var hasTimeValues = false;
                        $('#editsession .edit_added_grp:visible').each(function () {
                            var timeInputs = $(this).find('input[type="time"]');
                            var startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : '';
                            var endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : '';
                            if (startTime || endTime) { hasTimeValues = true; return false; }
                        });

                        if (hasTimeValues) {
                            if (!editValidateGroupTimesFilled()) return false;
                            if (!editValidateGroupTimes()) return false;
                        }

                        var groupCount = $('#editsession .edit_added_grp:visible').length + 1;
                        var $clone = $('#editsession .edit_added_grp:first').clone();

                        $clone.find('.registration-pop_body_box_heading span:first').text('Group ' + groupCount);
                        $clone.find('.edit_existing_theme_id').val('');

                        $clone.find('input[name="schedule_theme_title[]"]').val('');
                        $clone.find('input[type="text"]').val('');
                        $clone.find('input[type="time"]').val('');
                        $clone.find('select').prop('selectedIndex', 0);

                        $clone.find('.edit_Sequence').val('');
                        $clone.find('.edit_topic_duration').val('');
                        $clone.find('.edit_topic_title').val('');
                        $clone.find('.edit_Tag').val('');
                        $clone.find('.edit_Content').val('');
                        $clone.find('.edit_topicolor').prop('selectedIndex', 0);
                        $clone.find('.edit_Permanent').prop('checked', false);

                        $clone.find('.edit_talkContainer').empty();
                        $clone.find('.edit_hidden-topic').remove();
                        $clone.find('.edit_accm_add_empty').show();

                        $clone.find('.edit_participant-row-topic').not(':first').remove();
                        $clone.find('.edit_participant-row-topic:first select').prop('selectedIndex', 0);
                        $clone.find('.edit_participant-row-topic:first .edit_faculty-select').val(null);

                        $clone.find('.edit_participant-row-group').not(':first').remove();
                        $clone.find('.edit_participant-row-group:first select').prop('selectedIndex', 0);
                        $clone.find('.edit_participant-row-group:first .edit_faculty-select').val(null);

                        $clone.find('input[type="hidden"]').not('.edit_existing_theme_id').remove();

                        $clone.find('.select2').remove();
                        $clone.find('.edit_faculty-select')
                            .removeClass('select2-hidden-accessible')
                            .removeAttr('data-select2-id')
                            .removeAttr('tabindex')
                            .show();

                        if (editIsGroupMode) {
                            $clone.find('.edit_participant-row-group select[name="schedule_theme_participant_id[]"]').removeAttr('name');
                            $clone.find('.edit_participant-row-group input[name="schedule_theme_participant_name[]"]').removeAttr('name');
                            $clone.find('.edit_participant-row-group select[name="schedule_theme_participant_as[]"]').removeAttr('name');
                            $clone.find('input[name="schedule_theme_title[]"]').removeAttr('name');
                            $clone.find('input[name="theme_start[]"]').removeAttr('name');
                            $clone.find('input[name="theme_end[]"]').removeAttr('name');
                            $clone.find('select[name="color[]"]').removeAttr('name');
                            $clone.find('input[name="theme_start[]"]').prop('required', true);
                            $clone.find('input[name="theme_end[]"]').prop('required', true);
                        }

                        $(this).closest('h5').before($clone);
                        editInitFacultySelect($clone);
                        
                        // Mark exceeding after adding group
                        setTimeout(editMarkExceedingTopics, 300);
                    });

                    // ----------------------------------------------------
                    // Save Topic - MODIFIED to handle edit
                    // ----------------------------------------------------
                    var editEditingTopicKey = null;
                    var editEditingTopicElement = null;

                    $('#editsession').off(NS, '.edit_Edit-talk').on('click' + NS, '.edit_Edit-talk', function (e) {
                        e.preventDefault();
                        
                        if (editEditingTopicKey !== null) {
                            var $oldEditingItem = $('#editsession .edit_talk-item[data-topic-key="' + editEditingTopicKey + '"]');
                            if ($oldEditingItem.length) {
                                $oldEditingItem.css('opacity', '1');
                            }
                            var $oldGroup = $('#editsession .edit_topic_div');
                            if ($oldGroup.length) {
                                $oldGroup.find('.edit_topic_add').text('Save');
                                $oldGroup.data('editing-talk-item', null);
                            }
                            editEditingTopicKey = null;
                            editEditingTopicElement = null;
                        }
                        
                        var $talkItem = $(this).closest('.edit_talk-item');
                        var topicKey = $talkItem.data('topic-key');
                        var groupIndex = $talkItem.data('group-index') || 0;
                        var topicIndex = $talkItem.data('topic-index') || 0;
                        
                        editEditingTopicKey = topicKey;
                        editEditingTopicElement = $talkItem;
                        
                        var $group = $talkItem.closest('.edit_added_grp');
                        if ($group.length === 0) {
                            $group = $('#editsession .edit_topic_div');
                        }
                        
                        var $hiddenTopic = $('#editsession .edit_hidden-topic[data-topic-key="' + topicKey + '"]');
                        if ($hiddenTopic.length === 0) {
                            $hiddenTopic = $('#editHiddenContainer .edit_hidden-topic[data-topic-key="' + topicKey + '"]');
                        }
                        
                        if ($hiddenTopic.length > 0) {
                            $hiddenTopic.find('input').each(function() {
                                var $input = $(this);
                                var name = $input.attr('name');
                                var value = $input.val();
                                
                                if (name.indexOf('topic_title') !== -1) {
                                    $group.find('.edit_topic_title').val(value);
                                } else if (name.indexOf('topic_contant') !== -1) {
                                    $group.find('.edit_Content').val(value);
                                } else if (name.indexOf('topic_duration_min') !== -1) {
                                    $group.find('.edit_topic_duration').val(value);
                                } else if (name.indexOf('is_duration_permanent') !== -1) {
                                    if (value === 'Y') {
                                        $group.find('.edit_Permanent').prop('checked', true);
                                    } else {
                                        $group.find('.edit_Permanent').prop('checked', false);
                                    }
                                } else if (name.indexOf('sequence') !== -1) {
                                    $group.find('.edit_Sequence').val(value);
                                } else if (name.indexOf('reference_tag') !== -1) {
                                    $group.find('.edit_Tag').val(value);
                                } else if (name.indexOf('topicColor') !== -1) {
                                    $group.find('.edit_topicolor').val(value);
                                }
                            });
                            
                            var participantIds = [];
                            var participantNames = [];
                            var participantTypes = [];
                            
                            $hiddenTopic.find('input[name*="topic_theme_participant_id"]').each(function() {
                                var val = $(this).val();
                                if (val) {
                                    participantIds.push(val);
                                }
                            });
                            
                            $hiddenTopic.find('input[name*="topic_participant_name"]').each(function() {
                                var val = $(this).val();
                                if (val) {
                                    participantNames.push(val);
                                }
                            });
                            
                            $hiddenTopic.find('input[name*="topic_theme_participant_as"]').each(function() {
                                var val = $(this).val();
                                if (val) {
                                    participantTypes.push(val);
                                }
                            });
                            
                            var $participantContainer = $group.find('.edit_participantContainerTopic');
                            if ($participantContainer.length === 0) {
                                $participantContainer = $group.find('.accm_add_wrap.span_5');
                            }
                            
                            $participantContainer.find('.edit_participant-row-topic').not(':first').remove();
                            
                            if (participantIds.length > 0) {
                                var $firstRow = $participantContainer.find('.edit_participant-row-topic:first');
                                
                                var $select = $firstRow.find('.edit_faculty-select');
                                var option = new Option(participantNames[0] || participantIds[0], participantIds[0], true, true);
                                $select.append(option).trigger('change');
                                $select.val(participantIds[0]).trigger('change');
                                
                                $firstRow.find('.edit_topic_participant_type').val(participantTypes[0] || '');
                                
                                for (var i = 1; i < participantIds.length; i++) {
                                    var typeOptions = editGetParticipantTypeOptions();
                                    
                                    var $newRow = $(
                                        '<div class="accm_add_box bg-transparent py-0 pl-0 edit_participant-row-topic">' +
                                            '<div class="form_grid">' +
                                                '<div class="frm_grp span_2">' +
                                                    '<p class="frm-head">Faculty Name</p>' +
                                                    '<select class="edit_faculty-select" style="width:100%"></select>' +
                                                '</div>' +
                                                '<div class="frm_grp span_2">' +
                                                    '<p class="frm-head">Nature of Participant</p>' +
                                                    '<select class="edit_topic_participant_type">' + typeOptions + '</select>' +
                                                '</div>' +
                                                '<a href="#" class="edit_participant-delete-topic accm_delet icon_hover badge_danger action-transparent"><i class="fal fa-trash-alt"></i></a>' +
                                            '</div>' +
                                        '</div>'
                                    );
                                    
                                    $participantContainer.append($newRow);
                                    editInitFacultySelect($newRow);
                                    
                                    var $newSelect = $newRow.find('.edit_faculty-select');
                                    var newOption = new Option(participantNames[i] || participantIds[i], participantIds[i], true, true);
                                    $newSelect.append(newOption).trigger('change');
                                    $newSelect.val(participantIds[i]).trigger('change');
                                    $newRow.find('.edit_topic_participant_type').val(participantTypes[i] || '');
                                }
                            }
                            
                            $group.find('.edit_topic_add').text('Update');
                            $group.data('editing-talk-item', $talkItem);
                            $talkItem.css('opacity', '0.5');
                            $group.find('.edit_topic_title').focus();
                        }
                    });

                    // Save Topic - with duration validation
                    $('#editsession').off(NS, '.edit_topic_add').on('click' + NS, '.edit_topic_add', function (e) {
                        e.preventDefault();
                        editRemoveGroupFieldNames(false);

                        var $group = $(this).closest('.edit_added_grp');
                        var groupIndex = 0, isInGroup = false;

                        if (editIsGroupMode && $group.length > 0) {
                            groupIndex = $('#editsession .edit_added_grp:visible').index($group);
                            isInGroup = true;
                        } else {
                            $group = $('#editsession .edit_topic_div');
                            groupIndex = 0;
                            isInGroup = false;
                        }

                        var isEditing = editEditingTopicKey !== null;
                        var $editingTalkItem = $group.data('editing-talk-item');

                        var $sequence = $group.find('.edit_Sequence');
                        var $duration = $group.find('.edit_topic_duration');
                        var $title = $group.find('.edit_topic_title');
                        var $topicolor = $group.find('.edit_topicolor');
                        var $tag = $group.find('.edit_Tag');
                        var $content = $group.find('.edit_Content');
                        var $permanentRadio = $group.find('.edit_Permanent');

                        var sequence = $sequence.val();
                        var duration = $duration.val();
                        var title = $title.val();
                        var topicolor = $topicolor.val();
                        var tag = $tag.val();
                        var content = $content.val();

                        if (!sequence) { alert('Please enter Sequence'); $sequence.focus(); return false; }
                        if (!duration) { alert('Please enter Duration'); $duration.focus(); return false; }
                        if (!title) { alert('Please enter Topic Title'); $title.focus(); return false; }

                        // ==========================================================
                        // DURATION VALIDATION - SHOW ALERT BUT ALLOW SAVE
                        // ==========================================================
                    var durationNum = parseInt(duration, 10);
                        if (isInGroup) {
                            var groupDuration = editGetGroupDuration($group);
                            if (groupDuration > 0) {
                                // Get all topics duration including the one being edited
                                var currentTotal = editGetGroupTopicsDuration($group);
                                
                                // If editing, subtract the old topic's duration to avoid double counting
                                var oldDuration = 0;
                                if (isEditing && $editingTalkItem && $editingTalkItem.length > 0) {
                                    var oldDurationText = $editingTalkItem.find('n').text().trim();
                                    var oldMatch = oldDurationText.match(/(\d+)/);
                                    if (oldMatch) {
                                        oldDuration = parseInt(oldMatch[1], 10);
                                    }
                                    currentTotal = currentTotal - oldDuration; // Remove old duration
                                }
                                
                                var newTotal = currentTotal + durationNum;
                                if (newTotal > groupDuration) {
                                    var groupTitle = $group.find('input[name="schedule_theme_title[]"]').val() || 'Unnamed Group';
                                    alert('⚠️ Warning: Topic will exceed group duration!\n\n' +
                                        '📊 Current Topics Total: ' + currentTotal + ' min\n' +
                                        '📝 New Topic Duration: ' + durationNum + ' min\n' +
                                        '📈 New Total: ' + newTotal + ' min\n' +
                                        '⏰ Group Duration: ' + groupDuration + ' min\n\n' +
                                        '⚠️ The topic will be saved but marked in RED.');
                                }
                            }
                        } else {
                            var sessionDuration = editGetSessionDuration();
                            if (sessionDuration > 0) {
                                // Get all topics duration including the one being edited
                                var currentTotal = editGetAllTopicsDuration();
                                
                                // If editing, subtract the old topic's duration to avoid double counting
                                var oldDuration = 0;
                                if (isEditing && $editingTalkItem && $editingTalkItem.length > 0) {
                                    var oldDurationText = $editingTalkItem.find('n').text().trim();
                                    var oldMatch = oldDurationText.match(/(\d+)/);
                                    if (oldMatch) {
                                        oldDuration = parseInt(oldMatch[1], 10);
                                    }
                                    currentTotal = currentTotal - oldDuration; // Remove old duration
                                }
                                
                                var newTotal = currentTotal + durationNum;
                                if (newTotal > sessionDuration) {
                                    alert('⚠️ Warning: Topic will exceed session duration!\n\n' +
                                        '📊 Current Topics Total: ' + currentTotal + ' min\n' +
                                        '📝 New Topic Duration: ' + durationNum + ' min\n' +
                                        '📈 New Total: ' + newTotal + ' min\n' +
                                        '⏰ Session Duration: ' + sessionDuration + ' min\n\n' +
                                        '⚠️ The topic will be saved but marked in RED.');
                                }
                            }
                        }
                        var isPermanent = $permanentRadio.is(':checked') ? 'Y' : 'N';

                        var participants = [];
                        var $participantContainer = $group.find('.edit_participantContainerTopic');
                        var $participantRows = $participantContainer.find('.edit_participant-row-topic');

                        $participantRows.each(function () {
                            var $row = $(this);
                            var facultySelect = $row.find('.edit_faculty-select');
                            var participantType = $row.find('.edit_topic_participant_type').val();
                            var facultyData = facultySelect.length ? facultySelect.select2('data') : [];

                            var participantId = null, participantName = '';
                            if (facultyData && facultyData.length > 0 && facultyData[0] && facultyData[0].id) {
                                participantId = facultyData[0].id;
                                participantName = facultyData[0].text || '';
                            }

                            if (participantId) {
                                participants.push({ id: participantId, name: participantName, participantType: participantType || '' });
                            }
                        });

                        var topicKey, topicIndex;

                        if (isEditing && $editingTalkItem && $editingTalkItem.length > 0) {
                            topicKey = $editingTalkItem.attr('data-topic-key');
                            topicIndex = parseInt($editingTalkItem.attr('data-topic-index'), 10) || 0;
                        } else {
                            var maxIndex = -1;
                            $group.find('.edit_talk-item').each(function () {
                                var idx = parseInt($(this).attr('data-topic-index'), 10);
                                if (!isNaN(idx) && idx > maxIndex) maxIndex = idx;
                            });
                            topicIndex = maxIndex + 1;
                            topicKey = (isInGroup ? 'edit_topic_' : 'edit_topic_ng_') + topicIndex + '_group_' + groupIndex;
                        }
                        
                        var existingTopicId = '';
                        if (isEditing && $editingTalkItem && $editingTalkItem.length > 0) {
                            var topicKeyEdit = $editingTalkItem.data('topic-key');
                            var $existingHidden = $('#editsession .edit_hidden-topic[data-topic-key="' + topicKeyEdit + '"]');
                            if ($existingHidden.length > 0) {
                                $existingHidden.find('input[name*="topic_id"]').each(function() {
                                    var val = $(this).val();
                                    if (val && val !== '') {
                                        existingTopicId = val;
                                    }
                                });
                            }
                        }
                        
                        $('#editsession .edit_hidden-topic[data-topic-key="' + topicKey + '"]').remove();
                        $('#editHiddenContainer .edit_hidden-topic[data-topic-key="' + topicKey + '"]').remove();

                        if (isEditing && $editingTalkItem && $editingTalkItem.length > 0) {
                            $editingTalkItem.remove();
                        }

                        $('.edit_talk-item').css('opacity', '1');
                        $group.data('editing-talk-item', null);
                        editEditingTopicKey = null;
                        editEditingTopicElement = null;
                        $group.find('.edit_topic_add').text('Save');

                        var $topicContainer = $group.find('.edit_talkContainer');
                        if ($topicContainer.length === 0) {
                            $topicContainer = $('<div class="accm_add_wrap pl-0 pr-0 edit_talkContainer"></div>');
                            $group.find('.edit_accm_add_empty').after($topicContainer);
                        }

                        var hidden = $('<div class="edit_hidden-topic" data-topic-key="' + topicKey + '" data-group-index="' + groupIndex + '" data-topic-index="' + topicIndex + '">' +
                            '<input type="hidden" name="topic_id[' + groupIndex + '][]" value="'+existingTopicId+'">' +
                            '<input type="hidden" name="topic_title[' + groupIndex + '][]" value="' + escapeHtmlEdit(title) + '">' +
                            '<input type="hidden" name="topic_contant[' + groupIndex + '][]" value="' + escapeHtmlEdit(content || '') + '">' +
                            '<input type="hidden" name="topic_duration_min[' + groupIndex + '][]" value="' + escapeHtmlEdit(duration) + '">' +
                            '<input type="hidden" name="is_duration_permanent[' + groupIndex + '][]" value="' + isPermanent + '">' +
                            '<input type="hidden" name="sequence[' + groupIndex + '][]" value="' + escapeHtmlEdit(sequence) + '">' +
                            '<input type="hidden" name="reference_tag[' + groupIndex + '][]" value="' + escapeHtmlEdit(tag || '') + '">' +
                            '<input type="hidden" name="topicColor[' + groupIndex + '][]" value="' + escapeHtmlEdit(topicolor) + '">' +
                            '<input type="hidden" name="importFromAbstract[' + groupIndex + '][]" value="N">' +
                            '</div>');

                        if (participants.length > 0) {
                            participants.forEach(function (p) {
                                hidden.append(
                                    '<input type="hidden" name="topic_theme_participant_id[' + groupIndex + '][' + topicIndex + '][]" value="' + p.id + '">' +
                                    '<input type="hidden" name="topic_theme_participant_as[' + groupIndex + '][' + topicIndex + '][]" value="' + escapeHtmlEdit(p.participantType || '') + '">' +
                                    '<input type="hidden" name="topic_isFaculty[' + groupIndex + '][' + topicIndex + '][]" value="Faculty">' +
                                    '<input type="hidden" name="topic_participant_name[' + groupIndex + '][' + topicIndex + '][]" value="' + escapeHtmlEdit(p.name || '') + '">'
                                );
                            });
                        } else {
                            hidden.append(
                                '<input type="hidden" name="topic_theme_participant_id[' + groupIndex + '][' + topicIndex + '][0]" value="">' +
                                '<input type="hidden" name="topic_theme_participant_as[' + groupIndex + '][' + topicIndex + '][0]" value="">' +
                                '<input type="hidden" name="topic_isFaculty[' + groupIndex + '][' + topicIndex + '][0]" value="">' +
                                '<input type="hidden" name="topic_participant_name[' + groupIndex + '][' + topicIndex + '][0]" value="">'
                            );
                        }

                        $group.append(hidden);

                        var participantText = participants.map(function (p) { return p.name; }).join(', ');
                        $group.find('.edit_accm_add_empty').hide();

                        var $talkItem = $(
                            '<div class="accm_add_box pl-0 pr-0 edit_talk-item" data-sequence="' + (parseInt(sequence, 10) || 0) + '" data-topic-key="' + topicKey + '" data-group-index="' + groupIndex + '" data-topic-index="' + topicIndex + '">' +
                                '<li class="pg_shdl_talk_box_li align-items-center">' +
                                    '<div class="pg_shdl_talk_box_li_left">' +
                                        '<n class="text-left">' + escapeHtmlEdit(duration) + ' m<br>' + escapeHtmlEdit(tag) + '</n>' +
                                        '<k><g><span>' + escapeHtmlEdit(title) + '</span></g><j><span>' + escapeHtmlEdit(participantText) + '</span></j></k>' +
                                    '</div>' +
                                    '<div class="action_div action">' +
                                        '<a href="javascript:void(0)" class="icon_hover badge_danger br-5 w-auto action-transparent edit_Edit-talk"><i class="fal fa-pencil"></i></a>' +
                                        '<a href="javascript:void(0)" class="icon_hover badge_danger br-5 w-auto action-transparent edit_delete-talk"><i class="fal fa-trash-alt"></i></a>' +
                                    '</div>' +
                                '</li>' +
                            '</div>'
                        );

                        $topicContainer.append($talkItem);

                        var items = $group.find('.edit_talk-item').get();
                        if (items.length > 0) {
                            items.sort(function (a, b) { return parseInt($(a).data('sequence'), 10) - parseInt($(b).data('sequence'), 10); });
                            var $parent = $group.find('.edit_talk-item').first().parent();
                            $.each(items, function (idx, item) { $parent.append(item); });
                        }

                        $sequence.val('');
                        $duration.val('');
                        $title.val('');
                        $content.val('');
                        $tag.val('');
                        $topicolor.prop('selectedIndex', 0);
                        $permanentRadio.prop('checked', false);

                        $participantContainer.find('.edit_participant-row-topic').not(':first').remove();
                        var $firstRow = $participantContainer.find('.edit_participant-row-topic:first');
                        $firstRow.find('select').prop('selectedIndex', 0);

                        var $facultySelect = $firstRow.find('.edit_faculty-select');
                        $facultySelect.val(null).trigger('change');
                        $facultySelect.empty().trigger('change');
                        $facultySelect.select2('destroy');
                        $facultySelect.select2(editFacultySelectConfig());

                        $firstRow.find('.edit_topic_participant_type').prop('selectedIndex', 0);

                        $sequence.focus();
                        
                        // Mark exceeding after saving topic
                        setTimeout(editMarkExceedingTopics, 300);
                    });

                    // ----------------------------------------------------
                    // Delete Talk - with red marking update
                    // ----------------------------------------------------
                    $('#editsession').off(NS, '.edit_delete-talk').on('click' + NS, '.edit_delete-talk', function (e) {
                        e.preventDefault();
                        if(confirm("Are you sure you want to delete this participant?")){
                            var $talkItem = $(this).closest('.edit_talk-item');
                            var topicKey = $talkItem.data('topic-key');

                            $('#editsession .edit_hidden-topic[data-topic-key="' + topicKey + '"]').remove();
                            $('#editHiddenContainer .edit_hidden-topic[data-topic-key="' + topicKey + '"]').remove();
                            $talkItem.remove();

                            var $group = $(this).closest('.edit_added_grp');
                            if ($group.length === 0) $group = $('#editsession .edit_topic_div');

                            if ($group.find('.edit_talk-item').length === 0) {
                                $group.find('.edit_accm_add_empty').show();
                            }
                            
                            // Mark exceeding after deleting topic
                            setTimeout(editMarkExceedingTopics, 300);
                        }
                    });

                    // ----------------------------------------------------
                    // Submit handler
                    // ----------------------------------------------------
                    $('#frmEditSession').off(NS).on('submit' + NS, function (e) {
                        var timingCheckbox = $('#editsession .edit_timingCheckbox');
                        var hiddenTiming = $('#parallel_timing_hidden');
                        
                        if (timingCheckbox.is(':checked')) {
                            timingCheckbox.val('Y');
                            hiddenTiming.val('Y');
                        } else {
                            timingCheckbox.val('N');
                            hiddenTiming.val('N');
                        }
                        hiddenTiming.attr('name', 'parallel_timing');

                        var noTheme = $('#editsession .edit_noThemeHidden').val();
                        
                        if (noTheme === 'N' || editIsGroupMode) {
                            $('#editsession .edit_added_grp:visible').each(function() {
                                var $group = $(this);
                                $group.find('input[name="schedule_theme_title[]"]').attr('name', 'schedule_theme_title[]');
                                $group.find('input[placeholder="Group Name"]').attr('name', 'schedule_theme_title[]');
                                var timeInputs = $group.find('input[type="time"]');
                                if (timeInputs.length >= 2) {
                                    timeInputs.eq(0).attr('name', 'theme_start[]');
                                    timeInputs.eq(1).attr('name', 'theme_end[]');
                                }
                                $group.find('select[name="color[]"]').attr('name', 'color[]');
                                $group.find('.edit_participant-row-group').each(function() {
                                    var $row = $(this);
                                    $row.find('select[name="schedule_theme_participant_id[]"]').attr('name', 'schedule_theme_participant_id[]');
                                    $row.find('input[name="schedule_theme_participant_name[]"]').attr('name', 'schedule_theme_participant_name[]');
                                    $row.find('select[name="schedule_theme_participant_as[]"]').attr('name', 'schedule_theme_participant_as[]');
                                    $row.find('select:not(.edit_faculty-select)').each(function() {
                                        if (!$(this).attr('name') || $(this).attr('name') === '') {
                                            $(this).attr('name', 'schedule_theme_participant_as[]');
                                        }
                                    });
                                });
                            });
                        }

                        if (!editValidateGroupTimesFilled()) { e.preventDefault(); return false; }
                        if (!editValidateGroupTimes()) { e.preventDefault(); return false; }

                        $('#editHiddenContainer').empty();

                        var sessionStartTime = $('#editsession input[name="session_strating_time"]').val();
                        var sessionEndTime = $('#editsession input[name="session_ending"]').val();
                        var sessionColor = $('#edit_sessioncolor').val() || '#DBDBDB';

                        if (noTheme === 'Y' || !editIsGroupMode) {
                            var existingThemeId = $('#editsession .edit_existing_theme_id').val() || '';
                            if (existingThemeId === '') {
                                var $firstGroup = $('#editsession .edit_added_grp:first');
                                if ($firstGroup.length > 0) {
                                    existingThemeId = $firstGroup.find('.edit_existing_theme_id').val() || '';
                                }
                            }
                            
                            $('#editHiddenContainer').append(
                                '<input type="hidden" name="existing_theme_id[0]" value="' + existingThemeId + '">' +
                                '<input type="hidden" name="theme_start[0]" value="' + sessionStartTime + '">' +
                                '<input type="hidden" name="theme_end[0]" value="' + sessionEndTime + '">' +
                                '<input type="hidden" name="schedule_theme_title[0]" value="">' +
                                '<input type="hidden" name="color[0]" value="' + sessionColor + '">'
                            );

                            var $topicDiv = $('#editsession .edit_topic_div');
                            var talkItems = $topicDiv.find('.edit_talk-item');
                            var topicIndex = 0;
                            var processedKeys = [];
                            
                            talkItems.each(function () {
                                var $talkItem = $(this);
                                var topicKey = $talkItem.data('topic-key');
                                
                                if (processedKeys.indexOf(topicKey) !== -1) return;
                                processedKeys.push(topicKey);
                                
                                var $hiddenTopic = $('#editsession .edit_hidden-topic[data-topic-key="' + topicKey + '"]');
                                
                                if ($hiddenTopic.length > 0) {
                                    var $clone = $hiddenTopic.clone();
                                    
                                    $clone.find('input').each(function () {
                                        var $input = $(this);
                                        var name = $input.attr('name');
                                        if (name) {
                                            var newName = name.replace(/\[(\d+)\]/, '[0]');
                                            newName = newName.replace(/\]\[(\d+)\]/, '][' + topicIndex + ']');
                                            $input.attr('name', newName);
                                        }
                                    });
                                    
                                    $clone.data('group-index', 0);
                                    $clone.data('topic-index', topicIndex);
                                    
                                    var oldKey = $clone.data('topic-key');
                                    var newKey = oldKey.replace(/group_\d+/, 'group_0').replace(/topic_\d+/, 'topic_' + topicIndex);
                                    $clone.data('topic-key', newKey);
                                    
                                    $('#editHiddenContainer').append($clone);
                                    $hiddenTopic.remove();
                                    topicIndex++;
                                }
                            });

                        } else {
                            var groupIndex = 0;
                            var processedThemeIds = [];
                            
                            $('#editsession .edit_added_grp:visible').each(function () {
                                var $group = $(this);
                                var existingThemeId = $group.find('.edit_existing_theme_id').val() || '';
                                
                                if (existingThemeId !== '' && processedThemeIds.indexOf(existingThemeId) !== -1) {
                                    return;
                                }
                                if (existingThemeId !== '') {
                                    processedThemeIds.push(existingThemeId);
                                }

                                var groupTitle = $group.find('input[name="schedule_theme_title[]"]').val() || '';
                                if (groupTitle === '') {
                                    groupTitle = $group.find('input[placeholder="Group Name"]').val() || '';
                                }
                                
                                var groupColor = $group.find('select[name="color[]"]').val() || '#DBDBDB';
                                var timeInputs = $group.find('input[type="time"]');
                                var startTime = timeInputs.length >= 2 ? timeInputs.eq(0).val() : sessionStartTime;
                                var endTime = timeInputs.length >= 2 ? timeInputs.eq(1).val() : sessionEndTime;

                                if (groupTitle !== '' || startTime !== '' || endTime !== '' || existingThemeId !== '') {
                                    $('#editHiddenContainer').append(
                                        '<input type="hidden" name="existing_theme_id[' + groupIndex + ']" value="' + existingThemeId + '">' +
                                        '<input type="hidden" name="theme_start[' + groupIndex + ']" value="' + startTime + '">' +
                                        '<input type="hidden" name="theme_end[' + groupIndex + ']" value="' + endTime + '">' +
                                        '<input type="hidden" name="schedule_theme_title[' + groupIndex + ']" value="' + escapeHtmlEdit(groupTitle) + '">' +
                                        '<input type="hidden" name="color[' + groupIndex + ']" value="' + groupColor + '">'
                                    );

                                    var participantCount = 0;
                                    var seenParticipantIds = [];
                                    
                                    $group.find('.edit_participant-row-group').each(function () {
                                        var $row = $(this);
                                        var facultySelect = $row.find('.edit_faculty-select');
                                        var facultyData = facultySelect.length ? facultySelect.select2('data') : [];
                                        
                                        var participantType = '';
                                        var typeSelect = $row.find('select[name="schedule_theme_participant_as[]"]');
                                        if (typeSelect.length > 0) {
                                            participantType = typeSelect.val() || '';
                                        } else {
                                            var otherSelects = $row.find('select:not(.edit_faculty-select)');
                                            if (otherSelects.length > 0) {
                                                participantType = otherSelects.eq(0).val() || '';
                                            }
                                        }

                                        var participantId = null;
                                        var participantName = '';
                                        
                                        if (facultyData && facultyData.length > 0 && facultyData[0] && facultyData[0].id) {
                                            participantId = facultyData[0].id;
                                            participantName = facultyData[0].text || '';
                                        } else {
                                            var selectVal = facultySelect.val();
                                            if (selectVal && selectVal !== '') {
                                                participantId = selectVal;
                                                participantName = $row.find('.edit_participant-name-group').val() || '';
                                            }
                                        }

                                        if (participantId && seenParticipantIds.indexOf(participantId) === -1) {
                                            seenParticipantIds.push(participantId);
                                            $('#editHiddenContainer').append(
                                                '<input type="hidden" name="schedule_theme_participant_id[' + groupIndex + '][' + participantCount + ']" value="' + participantId + '">' +
                                                '<input type="hidden" name="schedule_theme_participant_name[' + groupIndex + '][' + participantCount + ']" value="' + escapeHtmlEdit(participantName) + '">' +
                                                '<input type="hidden" name="schedule_theme_participant_as[' + groupIndex + '][' + participantCount + ']" value="' + escapeHtmlEdit(participantType) + '">'
                                            );
                                            participantCount++;
                                        }
                                    });

                                    var talkItems = $group.find('.edit_talk-item');
                                    var topicIndex = 0;
                                    var processedKeys = [];
                                    
                                    talkItems.each(function () {
                                        var $talkItem = $(this);
                                        var topicKey = $talkItem.data('topic-key');
                                        
                                        if (processedKeys.indexOf(topicKey) !== -1) return;
                                        processedKeys.push(topicKey);
                                        
                                        var $hiddenTopic = $('#editsession .edit_hidden-topic[data-topic-key="' + topicKey + '"]');
                                        
                                        if ($hiddenTopic.length > 0) {
                                            var $clone = $hiddenTopic.clone();
                                            $clone.find('input').each(function () {
                                                var $input = $(this);
                                                var name = $input.attr('name');
                                                var newName = name.replace(/\[(\d+)\]/, '[' + groupIndex + ']');
                                                newName = newName.replace(/\]\[(\d+)\]/, '][' + topicIndex + ']');
                                                $input.attr('name', newName);
                                            });
                                            
                                            $clone.data('group-index', groupIndex);
                                            $clone.data('topic-index', topicIndex);
                                            
                                            var oldKey = $clone.data('topic-key');
                                            var newKey = oldKey.replace(/group_\d+/, 'group_' + groupIndex).replace(/topic_\d+/, 'topic_' + topicIndex);
                                            $clone.data('topic-key', newKey);
                                            
                                            $('#editHiddenContainer').append($clone);
                                            $hiddenTopic.remove();
                                            topicIndex++;
                                        }
                                    });

                                    groupIndex++;
                                }
                            });
                        }

                        var participantIndex = 0;
                        var seenParticipantIds = {};
                        var uniqueParticipants = [];
                        
                        $('#edit_participantContainer .edit_participant-row').each(function () {
                            var $row = $(this);
                            var facultySelect = $row.find('.edit_faculty-select');
                            var facultyData = facultySelect.length ? facultySelect.select2('data') : [];
                            var participantType = $row.find('select[name="participant_type_session[]"]').val();
                            
                            var participantId = null;
                            var participantName = '';
                            
                            if (facultyData && facultyData.length > 0 && facultyData[0] && facultyData[0].id) {
                                participantId = facultyData[0].id;
                                participantName = facultyData[0].text || '';
                            } else {
                                var selectVal = facultySelect.val();
                                if (selectVal && selectVal !== '') {
                                    participantId = selectVal;
                                    participantName = $row.find('.edit_participant-name').val() || '';
                                }
                            }
                            
                            if (!participantId) {
                                return;
                            }
                            
                            if (!seenParticipantIds[participantId]) {
                                seenParticipantIds[participantId] = true;
                                uniqueParticipants.push({
                                    id: participantId,
                                    name: participantName,
                                    type: participantType || ''
                                });
                            }
                        });

                        for (var i = 0; i < uniqueParticipants.length; i++) {
                            var p = uniqueParticipants[i];
                            $('#editHiddenContainer').append(
                                '<input type="hidden" name="participant_id_session[' + i + ']" value="' + p.id + '">' +
                                '<input type="hidden" name="participant_name_session[' + i + ']" value="' + escapeHtmlEdit(p.name) + '">' +
                                '<input type="hidden" name="participant_type_session[' + i + ']" value="' + escapeHtmlEdit(p.type) + '">'
                            );
                        }
                
                            if (!confirm("Are you sure you want to update this session?")) {
                                e.preventDefault();
                                return false;
                            }
                            
                            return true;
                    });

                    // ==========================================================
                    // INITIAL MARK EXCEEDING ON LOAD
                    // ==========================================================
                    setTimeout(editMarkExceedingTopics, 500);

                    window.__editSessionReinit = arguments.callee;
                })();
            </script>
        </div>
        <script>
        function initEditsession (){
            }
            document.addEventListener("DOMContentLoaded", initEditsession);
        </script>
        <!-- New session pop up -->
    </div>
</div>
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
        url: 'program_schedule.php',
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
 document.addEventListener("click", function(e) {
        if (e.target.closest(".popup_close")) {
            e.preventDefault(); // stops form submit
            $(".pop_up_wrap").hide(); // blur appears
            $(".pop_up_body").hide();
        }


    });
    
</script>

</html>