<?php include_once("includes/source.php"); ?>

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
                        <li class="breadcrumb-item active" aria-current="page">Program Sessions</li>
                    </ol>
                </nav>
                <h2>Program Sessions</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
        </div>

        <div class="table_wrap">
            <table>
                <thead>
                    <tr>
                        <th class="sl">#</th>
                        <th>Sessions</th>
                        <th>Time</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sqlDateisting = array();
						$sqlDateisting['QUERY']		= "SELECT * FROM "._DB_PROGRAM_SCHEDULE_DATE_." WHERE `status` = 'A'";
						$resultDateListing   		= $mycms->sql_select($sqlDateisting);
						
						$Counter			 		= 0;
						if($resultDateListing)
						{
							foreach($resultDateListing as $keyDateListing=>$rowDate)
							{
                                 $sql = array();
								$sql['QUERY'] = "SELECT * 
												   FROM "._DB_MASTER_HALL_." 
												  WHERE status = 'A'  
												    AND id IN (SELECT session_hall_id FROM "._DB_PROGRAM_SCHEDULE_SESSION_." WHERE session_date_id = '".$rowDate['id']."')
											   ORDER BY hall_title";
								$resultHall  = $mycms->sql_select($sql);
								if($resultHall)
								{
									foreach($resultHall as $key=>$rowHall)
									{	
						?>
                    <tr>
                        <td colspan="4" style="font-weight:bold;">
                            <?=$rowDate['conf_date']?> - <?=$rowHall['hall_title']?>
                            <a href="manage_program_session.hallchairperson.print.php?hallId=<?=$rowHall['id']?>&dateId=<?=$rowDate['id']?><?=$searchString?>" target="_blank" style="float:right; color:#0000FF;">
                                CHAIR-PERSON CVs
                            </a>
                        </td>
                    </tr>
                    <? } } }  }?>
                    <?
                        $sqlSelectSession = array();
                        $sqlSelectSession['QUERY']        = "SELECT session.*																	  
                                                                FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
                                                                WHERE session.status = 'A' 
                                                                AND session.session_date_id = '".$rowDate['id']."'
                                                                AND session.session_hall_id = '".$rowHall['id']."'
                                                            ORDER BY (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60), (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)";
                                                            
                        $resultSession			 		 = $mycms->sql_select($sqlSelectSession);
                        if($resultSession)
                        {
                            $i=0;
                            foreach($resultSession as $keySchedule=>$rowSession)
                            {
                                $i++;
                                  $sqlFetchTheme = array();
                                $sqlFetchTheme['QUERY'] = " SELECT id
                                                                FROM "._DB_PROGRAM_SCHEDULE_THEME_." 
                                                                WHERE schedule_id = '".$rowSession['id']."'
                                                                AND status = 'A'  
                                                            ORDER BY (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60), (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)";													 
                                $resultTheme   = $mycms->sql_select($sqlFetchTheme);	
                                if($resultTheme)
                                {
						?>
                    <tr>
                        <td class="sl"><?=$i?></td>
                        <td><?=$rowSession['session_title']?></td>
                        <td>
                            <?php clock() ?> <?=$rowSession['session_start_time']?>-<?=$rowSession['session_end_time']?>
                        </td>
                       <td align="center" valign="top">
                            <a href="manage_program_session.print.php?schid=<?=$rowSession['id']?><?=$searchString?>" target="_blank">
                                <img src="images/print.jpg" style="width:20px;" />
                            </a>
                        </td>
                    </tr>
                    <? } } } ?>
                </tbody>
            </table>
        </div>
        <!-- <div class="bbp-pagination">
            <div class="bbp-pagination-count">Showing 1 to 10 of 150 entries</div>
            <span class="paginationDisplay">
                <div class="pagination"><a>1 of 15 Pages</a><a class="selected">1</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=1">2</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=2">3</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=1">&gt;&gt;</a> <a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=14">Last</a></div>
            </span>
        </div> -->
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
</script>

</html>