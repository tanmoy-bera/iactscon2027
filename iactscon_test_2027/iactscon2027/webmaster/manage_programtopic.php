<?php include_once("includes/source.php"); ?>

<body>
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
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Manage Program Topic</a></li>
                    </ol>
                </nav>
                <h2>Manage Program Topic</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input id="searchInput"  name="q" placeholder="Search by Topic name..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newparticipanttopic"><?php add(); ?>New Participant Topic</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <form method="post" name="frmSearch">
                <div class="filter_body">
                    <div>
                        <label>Title</label>
                        <input type="text" name="src_title" id="src_title" style="text-transform:uppercase;" value="<?= $_REQUEST['src_title'] ?>" />
                    </div>
                
                    <div>
                        <label>Reference</label>
                        <input type="text" name="src_reference" id="src_reference" style="text-transform:uppercase;" value="<?= $_REQUEST['src_reference'] ?>" />
                    </div>
                    <div>
                        <label>Date</label>
                            <select name="src_date" id="src_date">
                                <option value="">-- Select Date --</option>
                                <?php
                                $sqlSelectDate = array();
                                $sqlSelectDate['QUERY']		= "SELECT * FROM "._DB_PROGRAM_SCHEDULE_DATE_." 
                                                                WHERE `status` = ?";
                                $sqlSelectDate['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');	
                                                                
                                $resultDate         = $mycms->sql_select($sqlSelectDate);
                                if($resultDate)
                                {
                                    foreach($resultDate as $keyDate=>$rowDate)
                                    {
                                ?>
                                        <option value="<?=$rowDate['id']?>" <?=($_REQUEST['src_date']==$rowDate['id'])?"selected":""?>><?=$rowDate['conf_date']?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                    </div>
                     <div>
                        <label>Hall</label>
                            <select name="src_hall" id="src_hall">
                                    <option value="">----Select Hall----</option>
                                <?
                                $sql = array();
                                $sql['QUERY']		 = "SELECT * FROM "._DB_MASTER_HALL_." WHERE status = 'A' ORDER BY hall_title";
                                
                                    
                                
                                $result	     = $mycms->sql_select($sql);
                                $Counter	 = 0;
                                foreach($result as $key=>$value)
                                {
                                ?>
                                    
                                    <option value="<?=$value['id']?>" <?=($value['id']==$_REQUEST['src_hall'])?"selected":""?>><?=$value['hall_title']?></option>
                                <?
                                }
                                ?>
                            </select>
                    </div>
                      <div>
                        <label>Session</label>
                           <select name="src_session" id="src_session">
                                <option value="">----Select Session----</option>
                            <?
                            $sqlSelectSession = array();
                            $sqlSelectSession['QUERY']        = "SELECT *																  
                                                            FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
                                                            WHERE session.status = 'A' 
                                                        ORDER BY session_title";																   
                            $resultSession			 = $mycms->sql_select($sqlSelectSession);
                            if($resultSession)
                            {
                                foreach($resultSession as $k=>$rowSession)
                                {
                            ?>
                                <option value="<?=$rowSession['id']?>" <?=$rowSession['id']==$_REQUEST['src_session']?"selected":""?>>
                                <?=$rowSession['session_title']?>
                                </option>
                            <?
                                }
                            }
                            ?>
                            </select>
                    </div>
                      <div>
                        <label>Group</label>
                           <select name="src_theme" id="src_theme">
                                <option value="">----Select Group----</option>
                            <?
                                $sqlFetchTheme = array();
                                $sqlFetchTheme['QUERY'] = "  SELECT *
                                                        FROM "._DB_PROGRAM_SCHEDULE_THEME_."
                                                        WHERE status = 'A'
                                                        AND TRIM(theme_title) != '' 
                                                    ORDER BY theme_title";													 
                                $resultTheme   = $mycms->sql_select($sqlFetchTheme);	
                                foreach($resultTheme as $k=>$rowTheme)
                                {
                            ?>
                                <option value="<?=$rowTheme['id']?>" <?=($rowTheme['id']==$_REQUEST['src_theme'])?"selected":""?>>
                                <?=$rowTheme['theme_title']?>
                                </option>
                            <?
                                }
                            ?>
                            </select>	
                    </div>
                     <div>
                    <label>Allocated?</label>
                    <select name="src_unallocated" id="src_allocated">
                            <option value="">-- Select --</option>
                            <option value="" <?= ($_REQUEST['src_unallocated'] == '') ? 'selected' : '' ?>>NO</option>
                            <option value="Y" <?= ($_REQUEST['src_unallocated'] == 'Y') ? 'selected' : '' ?>>YES</option>
                        </select>
                    </div>
                </div>
             
            <div class="filter_bottom">
                <button onclick="clearFilters();"><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
            </form>
        </div>
        <? 		$loggedUserID     = $mycms->getLoggedUserId(); ?>
         
        <div class="table_wrap">
            <table>
                <thead>
                    <tr>
                        <th class="sl">#</th>
                        <th>Title</th>
                        <th>Allocation</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $searchCondition        = "";
						if($_REQUEST['src_title']!="")
						{
							$searchCondition   .= " AND tpk.topic_title LIKE '%".$_REQUEST['src_title']."%'";
						}
						if($_REQUEST['src_reference']!="")
						{
							$searchCondition   .= " AND tpk.reference_tag LIKE '%".$_REQUEST['src_reference']."%'";
						}
						if($_REQUEST['src_date']!="")
						{
							$searchCondition   .= " AND session.session_date_id = '".$_REQUEST['src_date']."'";
						}
						if($_REQUEST['src_hall']!="")
						{
							$searchCondition   .= " AND session.session_hall_id = '".$_REQUEST['src_hall']."'";
						}
						if($_REQUEST['src_session']!="")
						{
							$searchCondition   .= " AND thm.schedule_id = '".$_REQUEST['src_session']."'";
						}
						if($_REQUEST['src_theme']!="")
						{
							$searchCondition   .= " AND tpk.schedule_theme_id = '".$_REQUEST['src_theme']."'";
						}
						if($_REQUEST['src_unallocated']!="")
						{
							$searchCondition   .= " AND (tpk.schedule_theme_id IS NULL OR tpk.schedule_theme_id = '')";
						}
                         if (!empty($_GET['q'])) {

                            $q = trim($_GET['q']);
                            $words = preg_split('/\s+/', $q);

                            $wordConditions = [];

                            foreach ($words as $word) {

                                $word = addslashes($word); // you can later upgrade to prepared

                                $wordConditions[] = "(
                                    topic_title LIKE '%$word%' 
                                   
                                )";
                            }

                            // Each word must match somewhere
                            $searchCondition .= " AND (" . implode(" AND ", $wordConditions) . ")";
                        }
						$sqlFetchTopic = array();
						$sqlFetchTopic['QUERY'] = "  SELECT tpk.*,
												   (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
												   (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins,
												   thm.theme_title, thm.theme_time_start, thm.theme_time_end,
												   session.session_title, session.session_start_time, session.session_end_time, session.session_date_id,
												   hall.hall_title,session.session_hall_id,
												   venue.id AS venue_id,
												   venue.program_venue,
												   scheduleDate.conf_date AS session_date  
												   
											  FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." tpk
										
								   LEFT OUTER JOIN "._DB_PROGRAM_SCHEDULE_THEME_." thm
												ON thm.id = tpk.schedule_theme_id
											  
								   LEFT OUTER JOIN "._DB_PROGRAM_SCHEDULE_SESSION_." session
												ON session.id = thm.schedule_id
												   
								   LEFT OUTER JOIN "._DB_PROGRAM_SCHEDULE_DATE_." scheduleDate 
												ON session.session_date_id = scheduleDate.id
												
								   LEFT OUTER JOIN "._DB_MASTER_HALL_." hall 
												ON session.session_hall_id = hall.id
												
								   LEFT OUTER JOIN "._DB_PROGRAM_SCHEDULE_VENUE_." venue 
												ON hall.hall_venue = venue.id
											 
											 WHERE tpk.status = ?
											 	   ".$searchCondition."
										  ORDER BY tpk.topic_title";
										  
						$sqlFetchTopic['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');	
						//$sqlFetchTopic['QUERY'] = "SELECT * FROM ".$cfg['DB.PROGRAM.SCHEDULE.TOPIC']." WHERE status = 'A' ".$searchCondition;

						$resultContent   = $mycms->sql_select_paginated('R001',$sqlFetchTopic,50);
                         $perPage = 50; // IMPORTANT: must match SQL LIMIT

                        $pageIndex = isset($_GET['_pgnR001_'])
                            ? (int)$_GET['_pgnR001_']
                            : 0;

                        $offset = $pageIndex * $perPage;
						$counter = 0;
						if($resultContent)
						{
                              $countertest = $offset + $key + 1;
								 $counter      = $countertest;
							$i=0;
							foreach($resultContent as $keyContent=>$rowContent)
							{
								$i++;
								$allocation	= array();
								if($rowContent['session_date']!='') $allocation[] = $rowContent['session_date'].' '.$rowContent['hall_title'];
								if($rowContent['session_title']!='') $allocation[] = $rowContent['session_title'];
								if($rowContent['theme_title']!='') $allocation[] = $rowContent['theme_title'];
								if($rowContent['topic_time_duration']!='') $allocation[] = $rowContent['topic_time_duration'].' mins.';
						?>
                    <tr>
                        <td class="sl"><?=$counter?></td>
                         <td><?=$rowContent['topic_title']?></td>
                        <td><?=implode('<br>',$allocation)?></td>
                      
                        <td class="action">
                            <div class="action_div">
                                <a data-tab="editparticipanttopic" data-id="<?=$rowContent['id']?>" data-title="<?=$rowContent['topic_title']?>" class="popup-btn icon_hover badge_secondary editparticipanttopicBtn action-transparent br-5 w-auto"><?php edit(); ?></a>
                                <a href="manage_program_topic.process.php?act=remove&amp;id=<?=$rowContent['id']?><?=$searchString?>" class="icon_hover badge_danger action-transparent br-5 w-auto"  onclick="return confirm('Do you really want to delete this record?');"><?php delete(); ?></a>
                            </div>
                        </td>
                    </tr>
                    <? $counter++; }  } ?>
                </tbody>
            </table>
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
    function clearFilters() {
    // Get the form
    var form = document.forms['frmSearch'];

    // Clear selects
    form.src_title.value = "";
    form.src_reference.value = "";
    form.src_unallocated.value = "";
    form.src_date.value = "";
    form.src_hall.value = "";
    form.src_session.value = "";
    form.src_theme.value = "";
   
    // Clear date input
    form.querySelector('input[type="date"]').value = "";

    // Optional: clear hidden act value (if you want a clean GET)
    // form.act.value = "";

    // Submit the form to PHP with empty values
    form.submit();
}
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
     $(document).on('click', '.editparticipanttopicBtn', function() {
        var id     = $(this).data('id');
        var title  = $(this).data('title');
       
        $('#topicTitleId').val(id);
        $('#editTitle').val(title);
     

        // if(status === 'A') {
        //     $('#edit_status_active').prop('checked', true);
        // } else {
        //     $('#edit_status_inactive').prop('checked', true);
        // }

        // Trigger the popup-btn functionality
     $('#editparticipanttopic').fadeIn(); // or your popup open function
    });
</script>

</html>