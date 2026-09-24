<?php 
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
include_once('includes/function.php');
?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Reviewer</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Reviewer</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Score Board</li>
                    </ol>
                </nav>
                <h2>Score Board</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Score Menu</h6>
                <button data-tab="reviewscorecategory" class="com_info_left_click icon_hover badge_default active">Review Score Category</button>
                <button data-tab="reviewscorelist" class="com_info_left_click icon_hover badge_default action-transparent">Review Score List</button>
            </div>
            <div class="com_info_right ">
                <div class="com_info_box active" id="reviewscorecategory">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_default">Review Score Category</n>
                                <a class="add mi-1 popup-btn" data-tab="newreviewcategory"><?php add() ?>Add Category</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="table_wrap">
                                    <?php
                                       $loggedUserId		= $mycms->getLoggedUserId();

                                        $sqlReviewCategory			  =	array();
                                        $sqlReviewCategory['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_REVIEW_SCORE_." 
                                                                        WHERE `status` IN ('A','I')
                                                                    ORDER BY `id` ASC";
                                        
                                        //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
                                        $resultReviewCategory = $mycms->sql_select($sqlReviewCategory);

                                        ?>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Score</th>
                                                <th>Category/Grade</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?
                                            if($resultReviewCategory)
                                            {
                                                
                                                foreach ($resultReviewCategory as $key => $value) {
                                                    if($value['status'] == 'A'){
                                                        $status = 'Active';
                                                    }
                                                    elseif($value['status'] == 'I'){
                                                        $status = 'Inactive';
                                                    } ?>

                                            <tr>
                                                <td class="sl"><?=$key+1?></td>
                                                <td><?=$value['score']==-1?'NA':$value['score']?></td>
                                                <td><?=$value['category']?></td>
                                                <td>
                                                    <div class="action_div">
                                                    <?php	
                                                        if($value['status']=='A'){
                                                            ?>
                                                            <a href="manage_faculty.process.php?act=<?=($value['status']=='A')?'InactivReviewCategory':'ActiveReviewCategory'?>&id=<?=$value['id']?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                            <a href="manage_faculty.process.php?act=<?=($value['status']=='A')?'InactivReviewCategory':'ActiveReviewCategory'?>&id=<?=$value['id']?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editreviewcategory" data-id="<?=$value['id']?>" data-category="<?=$value['category']?>"  data-score="<?=$value['score']?>" data-status="<?=$value['status']?>"  class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editReviewCatbtn"><?php edit() ?></a>
                                                        <a  href='manage_faculty.process.php?act=deleteReviewCategory&ID=<?=$value['id']?>&modified_by=<?=$loggedUserId?>'  onclick="return confirm('Do you really want to remove this record ?');"  class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete() ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php 
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
                <div class="com_info_box" id="reviewscorelist">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_default">Review Score List</n>
                                <a class="add mi-1 popup-btn" data-tab="newreviewscore"><?php add() ?>Add Score</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Name</th>
                                                <th>Max Score</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $loggedUserId		= $mycms->getLoggedUserId();

                                            $sqlReviewCategory			  =	array();
                                            $sqlReviewCategory['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_REVIEW_LIST_." 
                                                                            WHERE `status`='A'
                                                                        ORDER BY `id` ASC";
                                            
                                            //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
                                            $resultReviewCategory = $mycms->sql_select($sqlReviewCategory);

                                        if($resultReviewCategory)
                                        {
                                            
                                            foreach ($resultReviewCategory as $key => $value) {
                                                ?>

                                            <tr>
                                                <td class="sl"><?=$key+1?></td>
                                                <td><?=$value['abstract_name']?></td>
                                                <td><?=$value['full_marks']?></td>                                               
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editreviewscore" data-id="<?=$value['id']?>" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto revieweditbtnlist"><?php edit() ?></a>
                                                        <a   href='manage_faculty.process.php?act=deleteReviewList&ID=<?=$value['id']?>&modified_by=<?=$loggedUserId?>'  onclick="return confirm('Do you really want to remove this record ?');"  class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete() ?></a>
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
</body>
 <div class="pop_up_wrap" >
                    <div class="pop_up_inner">
        <div class="pop_up_body " id="newsession">
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
 
        <!-- Add review score category pop up -->
        <div class="pop_up_body" id="newreviewcategory" >
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Review Score Category</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form action="<?= _BASE_URL_ ?>webmaster/manage_faculty.process.php" method="post" name="frmadd" onsubmit="return onSubmitAction();">
                <input type="hidden" name="act" value="insertTopic" autocomplete="off">		
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Category</p>
                                    <select name="category" id="category" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Score">Score</option>
                                        <option value="Grade">Grade</option>
                                    </select>
                                </div>

                                <!-- GRADE -->
                                <div class="frm_grp span_4" id="gradeWrap" style="display:none;">
                                    <p class="frm-head">Grade</p>
                                    <select name="score" id="gradeSelect" disabled>
                                        <option value="">-- Select Grade --</option>
                                        <option value="Accepted">Accepted</option>
                                        <option value="Rejected">Rejected</option>
                                        <?php foreach (range('A', 'H') as $g) { ?>
                                            <option value="<?= $g ?>"><?= $g ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- SCORE -->
                                <div class="frm_grp span_4" id="scoreWrap" style="display:none;">
                                    <p class="frm-head">Score</p>
                                    <select name="score" id="scoreSelect" disabled>
                                        <option value="">-- Select Score --</option>
                                        <option value="-1">NA</option>
                                        <?php for ($i = 0; $i < 20; $i++) { ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Status</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="status" value="A">
                                            <span class="checkmark">Active</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="status" value="I">
                                            <span class="checkmark">Inactive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
              </form>
            </div>
        </div>
        <!-- Add review score category pop up -->
    
        <!-- Edit review score category pop up -->
        <div class="pop_up_body" id="editreviewcategory">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Review Score Category</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <? $loggedUserId = $mycms->getLoggedUserId(); ?>
                <form action="<?= _BASE_URL_ ?>webmaster/manage_faculty.process.php" method="post" name="frmadd" onsubmit="return onSubmitAction();">
                <input type="hidden" name="act" value="updateTopic" autocomplete="off">		
                <input type="hidden" name="topic_id" id="reviewCatId" autocomplete="off">	
                <input type="hidden" name="modified_by" value="<?= $loggedUserId ?>" autocomplete="off">		             
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                               <div class="frm_grp span_4">
                                    <p class="frm-head">Category</p>
                                    <select name="category" id="categoryReview" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Score">Score</option>
                                        <option value="Grade">Grade</option>
                                    </select>
                                </div>

                                <!-- GRADE -->
                                <div class="frm_grp span_4" id="gradeWrapEdit" style="display:none;">
                                    <p class="frm-head">Grade</p>
                                    <select name="score" id="gradeSelectEdit" disabled>
                                        <option value="">-- Select Grade --</option>
                                        <option value="Accepted">Accepted</option>
                                        <option value="Rejected">Rejected</option>
                                        <?php foreach (range('A', 'H') as $g) { ?>
                                            <option value="<?= $g ?>"><?= $g ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- SCORE -->
                                <div class="frm_grp span_4" id="scoreWrapEdit" style="display:none;">
                                    <p class="frm-head">Score</p>
                                    <select name="score" id="scoreSelectEdit" disabled>
                                        <option value="">-- Select Score --</option>
                                        <option value="-1">NA</option>
                                        <?php for ($i = 0; $i < 20; $i++) { ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Status</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="status" id="edit_cat_active" value="A">
                                            <span class="checkmark">Active</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="status" id="edit_cat_inactive" value="I">
                                            <span class="checkmark">Inactive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                    </div>
                </div>
              </form>
            </div>
        </div>
        <!-- Edit review score category pop up -->
     
        <!-- Add review score pop up -->
        <div class="pop_up_body" id="newreviewscore" >
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Review Score</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <?php
                $sqlReviewCategory = array();
                $sqlReviewCategory['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_REVIEW_SCORE_ . " 
                                                WHERE `status` ='A' 
                                            ORDER BY `id` ASC";


                $resultReviewCategory = $mycms->sql_select($sqlReviewCategory);


                $sqlCategory = array();
                $sqlCategory['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                                                WHERE `status` ='A'
                                            ORDER BY `id` ASC";


                $resultCategory = $mycms->sql_select($sqlCategory);
                ?>
                <form action="<?= _BASE_URL_ ?>webmaster/manage_faculty.process.php" method="post" name="frmadd" onsubmit="return onSubmitAction();">
                <input type="hidden" name="act" value="insertReviewList" autocomplete="off">		
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Name</p>
                                    <input type="text" name="abstract_name" id="abstract_name" required >
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Score Option</p>
                                    <select name="score_option" id="score_option"  required="" onchange="getScoreCatResult(this.value)">
                                        <option value="">Select Option</option>
                                        <option value="textbox">Textbox</option>
                                        <option value="dropdown">Radio Button</option>

                                    </select>
                                </div>
                                
                                <div class="frm_grp span_4" style="display: none;" id="score_cat">
                                    <p class="frm-head">Review Category</p>
                                    <select name="review_category_id[]"  id="review_category_id" style="width: 100%;" class="mySelect for category_id" multiple="multiple" required="">
              
                                        <?php
                                        if (count($resultReviewCategory) > 0) {
                                            foreach ($resultReviewCategory as $key => $value) {
                                                ?>
                                                    
                                                                    <option value="<?php echo $value['id']; ?>"><?= $value['score'] == -1 ? $value['category'] : $value['score']; ?></option>
                                                                    <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_4" id="score_cat">
                                    <p class="frm-head">Main Category</p>
                                    <select class="mySelect for category_id"  name="category_id[]" id="category_id_add"  multiple="multiple" style="width: 100%;">
                                       <?php
                                       if (count($resultCategory) > 0) {
                                           foreach ($resultCategory as $key => $value) {
                                               ?>
                                                    
                                                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['category']; ?></option>
                                                                    <?php
                                           }
                                       }
                                       ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_4"  id="sub_cat" style="display: none;">
                                    <p class="frm-head">Sub Category</p>
                                   <select name="sub_category_id[]" id="sub_category_id" class="mySelect for sub_category_id" multiple="multiple" style="width: 100%;">
              
                            
                                     </select>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Full Marks</p>
                                    <input  type="text" name="full_marks" id="full_marks"  >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <!-- Add review score pop up -->
        
        <!-- Edit review score pop up -->
        <div class="pop_up_body" id="editreviewscore">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Review Score</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <?php

                $loggedUserId = $mycms->getLoggedUserId();

                $review_cat_id = trim($_REQUEST['reviewerScoreId']);

                if (isset($review_cat_id) && !empty($review_cat_id)) {



                    $sqlReviewCategory = array();
                    $sqlReviewCategory['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_REVIEW_SCORE_ . " 
                                                WHERE `status` ='A' 
                                            ORDER BY `id` ASC";


                    $resultReviewCategory = $mycms->sql_select($sqlReviewCategory);

                    // $sqlReviewCategoryText			  =	array();
                    // $sqlReviewCategoryText['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_REVIEW_SCORE_." 
                    // 							  WHERE `status` ='A' AND `score`= -1 
                    // 						   ORDER BY `id` ASC";
                

                    // $resultReviewCategoryText = $mycms->sql_select($sqlReviewCategoryText);
                

                    $sqlReviewList = array();
                    $sqlReviewList['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_REVIEW_LIST_ . " 
                                                    WHERE `id` = ?";

                    $sqlReviewList['PARAM'][] = array('FILD' => 'id', 'DATA' => $review_cat_id, 'TYP' => 's');
                    //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'category', 'DATA' =>$abstract_topic_id,  'TYP' => 's');
                    $resultReviewList = $mycms->sql_select($sqlReviewList);

                    $category_id = json_decode($resultReviewList[0]['category_id']);

                    $array = implode("','", $category_id);

                    $sqlCategory = array();
                    $sqlCategory['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                                                WHERE `status` ='A'
                                            ORDER BY `id` ASC";


                    $resultCategory = $mycms->sql_select($sqlCategory);

                    $sqlSubCategory = array();
                    $sqlSubCategory['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
                                                WHERE `status` ='A' AND category IN ('" . $array . "')
                                            ORDER BY `id` ASC";


                    $resultSubCategory = $mycms->sql_select($sqlSubCategory);

                    //print_r($sqlSubCategory);
                

                    $allCat = json_decode($resultReviewList[0]['review_category_id']);
                    $category_id = json_decode($resultReviewList[0]['category_id']);
                    $sub_category_id = json_decode($resultReviewList[0]['sub_category_id']);
                    if ($resultReviewList[0]['score_option'] == 'dropdown') {
                        $clss = '';
                    } else {
                        $clss = 'none';
                    }
                }
                ?>
                    <form action="<?= _BASE_URL_ ?>webmaster/manage_faculty.process.php" method="post" name="frmadd" onsubmit="return onSubmitAction();">
                    <input type="hidden" name="act" value="updateReviewList" autocomplete="off">	
                    <input type="hidden" name="topic_id" value="<?= $review_cat_id ?>" autocomplete="off">	
                        
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4" id="tpcategory">
                                    <p class="frm-head">Name</p>
                                    <input  type="text" name="abstract_name" id="abstract_name" required  value="<?= $resultReviewList[0]['abstract_name'] ?>">
                                </div>
                                <div class="frm_grp span_4" id="tpcategory">
                                    <p class="frm-head">Score Option</p>
                                    <select name="score_option" id="score_option" required="" onchange="getScoreCatResult(this.value)">
                                        <option value="">Select Option</option>
                                        <option value="textbox"<?php if ($resultReviewList[0]['score_option'] == 'textbox') {
                                            echo 'selected';
                                        } ?>>Textbox</option>
                                        <option value="dropdown"<?php if ($resultReviewList[0]['score_option'] == 'dropdown') {
                                            echo 'selected';
                                        } ?>>Radio Button</option>
                                    </select>
                                </div>
                                 <div class="frm_grp span_4" style="display: <?= $clss ?>;" id="score_cat_edit">
                                    <p class="frm-head">Review Category</p>
                                    <select name="review_category_id[]" id="review_category_id_edit" class="mySelect for" multiple="multiple" style="width: 100%;">
                                       <?php
                                       if (count($resultReviewCategory) > 0) {
                                           foreach ($resultReviewCategory as $key => $value) {
                                               ?>
                                                    
                                                                    <option value="<?php echo $value['id']; ?>"<?php if (in_array($value['id'], $allCat)) {
                                                                           echo 'selected';
                                                                       } ?>><?php echo $value['score'] == -1 ? $value['category'] : $value['score']; ?></option>
                                                                    <?php
                                           }
                                       }
                                       ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_4" id="score_cat_edit">
                                    <p class="frm-head">Main Category</p>
                                    <select class="mySelect for category_id" name="category_id[]" id="category_id_edit"   multiple="multiple" style="width: 100%;">
                                        <?php
                                        if (count($resultCategory) > 0) {
                                            foreach ($resultCategory as $key => $value) {
                                                ?>
                                                    
                                                                    <option value="<?php echo $value['id']; ?>"<?php if (in_array($value['id'], $category_id)) {
                                                                           echo 'selected';
                                                                       } ?>><?php echo $value['category']; ?></option>
                                                                    <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_4" id="sub_cat_edit">
                                    <p class="frm-head">Sub Category</p>
                                    <select class="mySelect for sub_category_id" name="sub_category_id[]" id="sub_category_id_edit" multiple="multiple" style="width: 100%;">
                                     <?php
                                     if (count($resultSubCategory) > 0) {
                                         foreach ($resultSubCategory as $key => $value) {
                                             ?>
                                                    
                                                                    <option value="<?php echo $value['id']; ?>"<?php if (in_array($value['id'], $sub_category_id)) {
                                                                           echo 'selected';
                                                                       } ?>><?php echo $value['abstract_submission']; ?></option>
                                                                    <?php
                                         }
                                     }
                                     ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Full Marks</p>
                                    <input  type="text" name="full_marks" id="full_marks"  value="<?= $resultReviewList[0]['full_marks'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                    </div>
                </div>
              </form>
            </div>
        </div>
        <!-- Edit review score pop up -->
         </div>
        </div>
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
    (function() {
        const second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24;

        //I'm adding this section so I don't have to keep updating this pen every year :-)
        //remove this if you don't need it
        let today = new Date(),
            dd = String(today.getDate()).padStart(2, "0"),
            mm = String(today.getMonth() + 1).padStart(2, "0"),
            yyyy = 2026,
            nextYear = yyyy,
            dayMonth = "04/16/",
            birthday = dayMonth + yyyy;

        today = mm + "/" + dd + "/" + yyyy;
        if (today > birthday) {
            birthday = dayMonth + nextYear;
        }
        //end

        const countDown = new Date(birthday).getTime(),
            x = setInterval(function() {

                const now = new Date().getTime(),
                    distance = countDown - now;

                document.getElementById("days").innerText = Math.floor(distance / (day)),
                    document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
                    document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                    document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);
                if (distance < 0) {

                    $("#registration_countdown").hide(),
                        $("#registration_closed").show(),
                        $(".register").hide(),
                        clearInterval(x);
                }
            }, 0)
    }());
    (function() {
        const abssecond = 1000,
            absminute = abssecond * 60,
            abshour = absminute * 60,
            absday = abshour * 24;

        //I'm adding this section so I don't have to keep updating this pen every year :-)
        //remove this if you don't need it
        let abstoday = new Date(),
            absdd = String(abstoday.getDate()).padStart(2, "0"),
            absmm = String(abstoday.getMonth() + 1).padStart(2, "0"),
            absyyyy = 2026,
            absnextYear = absyyyy,
            absdayMonth = "07/14/",
            absbirthday = absdayMonth + absyyyy;

        abstoday = absmm + "/" + absdd + "/" + absyyyy;
        if (abstoday > absbirthday) {
            absbirthday = absdayMonth + absnextYear;
        }
        //end

        const abscountDown = new Date(absbirthday).getTime(),
            absx = setInterval(function() {

                const absnow = new Date().getTime(),
                    absdistance = abscountDown - absnow;

                document.getElementById("absdays").innerText = Math.floor(absdistance / (absday)),
                    document.getElementById("abshours").innerText = Math.floor((absdistance % (absday)) / (abshour)),
                    document.getElementById("absminutes").innerText = Math.floor((absdistance % (abshour)) / (absminute)),
                    document.getElementById("absseconds").innerText = Math.floor((absdistance % (absminute)) / abssecond);
                if (absdistance < 0) {
                    $("#abstract_end").innerText = "Submission Closed";
                    $("#abstract_countdown").hide(),
                        $("#abstract_end").show(),
                        $(".abs_sub").hide(),
                        clearInterval(x);
                }
            }, 0)
    }());
      //////////////////review category edit//////////
      // Show/hide Grade or Score depending on Category
        function toggleScoreGrade(category, $gradeWrap, $gradeSel, $scoreWrap, $scoreSel, resetValues) {
            // hide + disable both first
            $gradeWrap.hide();
            $scoreWrap.hide();
            $gradeSel.prop({ disabled: true, required: false });
            $scoreSel.prop({ disabled: true, required: false });

            if (resetValues) {
                $gradeSel.val('');
                $scoreSel.val('');
            }

            if (category === 'Score') {
                $scoreWrap.show();
                $scoreSel.prop({ disabled: false, required: true });
            } else if (category === 'Grade') {
                $gradeWrap.show();
                $gradeSel.prop({ disabled: false, required: true });
            }
        }

        // ADD popup
        $(document).on('change', '#category', function () {
            toggleScoreGrade(this.value,
                $('#gradeWrap'), $('#gradeSelect'),
                $('#scoreWrap'), $('#scoreSelect'), true);
        });

        // EDIT popup (when user changes category manually)
        $(document).on('change', '#categoryReview', function () {
            toggleScoreGrade(this.value,
                $('#gradeWrapEdit'), $('#gradeSelectEdit'),
                $('#scoreWrapEdit'), $('#scoreSelectEdit'), true);
        });
    $(document).on('click', '.editReviewCatbtn', function () {

        var id       = $(this).data('id');
        var category = $(this).data('category');
        var score    = $(this).data('score');
        var status   = $(this).data('status');

        $('#reviewCatId').val(id);
        $('#categoryReview').val(category);

        // show the right dropdown first (without wiping values)
        toggleScoreGrade(category,
            $('#gradeWrapEdit'), $('#gradeSelectEdit'),
            $('#scoreWrapEdit'), $('#scoreSelectEdit'), true);

        // then fill in the value
        if (category === 'Score') {
            $('#scoreSelectEdit').val(score);
        } else if (category === 'Grade') {
            $('#gradeSelectEdit').val(score);
        }

        if (status === 'A') {
            $('#edit_cat_active').prop('checked', true);
        } else {
            $('#edit_cat_inactive').prop('checked', true);
        }

        $('#editreviewcategory').fadeIn();
    });
    ////////////review category end////////////////////
  ///////////////////Reviewer Edit///////////////////////////
   $(document).on('click', '.revieweditbtnlist', function () {

    let reviewerScoreId = $(this).data('id');

    $.ajax({
        url: 'score_board.php',
        type: 'POST',
        data: { reviewerScoreId: reviewerScoreId },
        success: function (response) {

            $('#editreviewscore').html($(response).find('#editreviewscore').html());

            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditreviewscore();
           

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
       ///////////////////Reviewer end///////////////////////////
</script>
<script>
    function initEditreviewscore () {
     $(".mySelect").select2({
        minimumResultsForSearch: 5
    });
    $(document).ready(function() {

    $(document).on('change', '#category_id_edit', function() {
         var selectedValues = $(this).val();
        if(selectedValues)
        {
            var dataValue = "act=getMultiSubcat&catId="+selectedValues+"";
            
            $.ajax({
                    type : 'GET',
                    data: dataValue,
                    url : "manage_faculty.process.php",
                    dataType: "json",
                    success: function(data){
                        if(data!=false)
                        {
                                $('#sub_cat_edit').show();
                                $("#sub_category_id_edit").empty();

                                $.each(data, function(index, item) {
                                $("#sub_category_id_edit").append("<option value='" + item.id + "'>" + item.abstract_submission + "</option>");
                                console.log(item.id);

                            });
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                }).fail(function() {
                    alert("Something Wrong!! Could not update.");
                    //window.location.reload();
                });
        }
    });

});
}
document.addEventListener("DOMContentLoaded", initEditreviewscore);
    $(document).ready(function() {

    $(document).on('change', '#category_id_add', function() {
         var selectedValues = $(this).val();
        if(selectedValues)
        {
            var dataValue = "act=getMultiSubcat&catId="+selectedValues+"";
            
            $.ajax({
                    type : 'GET',
                    data: dataValue,
                    url : "manage_faculty.process.php",
                    dataType: "json",
                    success: function(data){
                        if(data!=false)
                        {
                                $('#sub_cat').show();
                                $("#sub_category_id").empty();

                                $.each(data, function(index, item) {
                                $("#sub_category_id").append("<option value='" + item.id + "'>" + item.abstract_submission + "</option>");
                                console.log(item.id);

                            });
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                }).fail(function() {
                    alert("Something Wrong!! Could not update.");
                    //window.location.reload();
                });
        }
    });

});
function getScoreCatResult(val)
{
    if(val=='dropdown')
    {
        $('#score_cat').show();
         $('#score_cat_edit').show();
    }
    else
    {
        $('#review_category_id').removeAttr('required');
        $('#score_cat').hide();

         $('#review_category_id_edit').removeAttr('required');
        $('#score_cat_edit').hide();
    }
}
  document.addEventListener("click", function(e) {
        if (e.target.closest(".popup_close")) {
            e.preventDefault(); // stops form submit
                    $(".pop_up_wrap").hide();
                $(".pop_up_body").hide();
        }
     });
</script>



</html>