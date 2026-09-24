<?php 
 include_once("includes/source.php");
 include_once('includes/init.php');
include_once('includes/function.workshop.php');
$cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>System Master</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">System Master</a></li>
                    </ol>
                </nav>
                <h2>Manage System Master</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>System Menu</h6>
                <button data-tab="combo" class="com_info_left_click icon_hover badge_primary active"><i class="fal fa-school"></i>Combo</button>
                <button data-tab="cutoff" class="com_info_left_click icon_hover badge_secondary action-transparent"><?php rupee() ?>Cutoff</button>
                <button data-tab="registration" class="com_info_left_click icon_hover badge_success action-transparent"><?php user() ?>Registration</button>
                <button data-tab="workshop" class="com_info_left_click icon_hover badge_info action-transparent"><?php workshop() ?>Workshop</button>
                <button data-tab="accommodation" class="com_info_left_click icon_hover badge_danger action-transparent"><?php hotel() ?>Accommodation</button>
                <button data-tab="dinner" class="com_info_left_click icon_hover badge_dark action-transparent"><?php dinner() ?>Dinner</button>
                <button data-tab="accompany" class="com_info_left_click icon_hover badge_default action-transparent"><?php duser() ?>Accompany</button>
            </div>
            <div class="com_info_right">
                
                <div class="com_info_box active" id="combo">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php credit() ?></span> Registration Tariff</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Registration Classification</th>
                                                <th class="text-right">Early Bird</th>
                                                <th class="text-right">Regular</th>
                                                <th class="text-right">Advance</th>
                                                <th class="text-right">Spot</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php conregi() ?></span>Registration Classification</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Combo</span><a class="add mi-1 popup-btn" data-tab="newcombo"><?php add() ?>Add Classification</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Registration Classification</th>
                                                <th class="text-right">Early Bird</th>
                                                <th class="text-right">Regular</th>
                                                <th class="text-right">Advance</th>
                                                <th class="text-right">Spot</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Delegate</td>
                                                <td class="text-right">INR 4000.00</td>
                                                <td class="text-right">INR 6000.00</td>
                                                <td class="text-right">INR 8000.00</td>
                                                <td class="text-right">INR 12000.00</td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="cutoff">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary"><?php conregi() ?></span> Registration Cutoff</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Cutoff</span><a class="add mi-1 popup-btn" data-tab="addregistrationcutoff"><?php add() ?>Add Cutoff</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Cutoff Title</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql_cal	=	array();
                                            $sql_cal['QUERY']	=	"SELECT *  
                                                                        FROM " . _DB_TARIFF_CUTOFF_ . " 
                                                                    WHERE status != 'D'";
                                            $res_cal			=	$mycms->sql_select($sql_cal);
                                            $i = 1;

                                            foreach ($res_cal as $key => $rowsl) {
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $i ?></td>
                                                <td><?= $rowsl['cutoff_title'] ?></td>
                                                <td><?= displayDateFormat($rowsl['start_date']) ?></td>
                                                <td><?= displayDateFormat($rowsl['end_date']) ?></td>
                                                <td>
                                                    <div class="action_div">
                                                         <?php	
                                                        if($rowsl['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                        <!-- <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span> -->
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                         <a href="javascript:void(0);" 
                                                            class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editCutOffBtn" 
                                                            data-tab="editregistrationcutoff"
                                                            data-id="<?= $rowsl['id'] ?>"
                                                            data-title="<?= htmlspecialchars($rowsl['cutoff_title']) ?>"
                                                            data-end_date="<?= $rowsl['end_date'] ?>"
                                                            data-start_date="<?= $rowsl['start_date'] ?>"
                                                             data-status="<?= $rowsl['status'] ?>"
                                                         >
                                                            <?php edit(); ?>
                                                        </a>
                                                        <!-- <a data-tab="editregistrationcutoff" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a> -->
                                                        <!-- <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a> -->
                                                        <a href="javascript:void(0);" 
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                            onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                            window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=deleteCutoff&id=<?= $rowsl['id'] ?>'; 
                                                                        }">
                                                                <?php delete(); ?>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?
                                                $i++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Conference Date</span><a class="add mi-1 popup-btn" data-tab="addconferencedate"><?php add() ?>Add Date</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Conference Dates</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql_cal	=	array();
                                            $sql_cal['QUERY']	=	"SELECT *  
                                                                    FROM " . _DB_CONFERENCE_DATE_ . " 
                                                                    WHERE status != 'D'";
                                            $res_cal			=	$mycms->sql_select($sql_cal);
                                            $i = 1;

                                            foreach ($res_cal as $key => $rowsl) {
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $i ?></td>
                                                <td><?= $rowsl['conf_date'] ?></td>
                                                <td>
                                                    <div class="action_div">
                                                         <?php	
                                                        if($rowsl['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?=($rowsl['status']=='A')?'InactiveDate':'ActiveDate'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?=($rowsl['status']=='A')?'InactiveDate':'ActiveDate'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                        <!-- <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span> -->
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="javascript:void(0);" 
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                            onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                            window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=deleteDate&id=<?= $rowsl['id'] ?>'; 
                                                                        }">
                                                                <?php delete(); ?>
                                                        </a>
                                                        <!-- <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><i class="fal fa-trash-alt"></i></a> -->
                                                    </div>
                                                </td>
                                            </tr>
                                            <?
                                                $i++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary"><?php workshop() ?></span> Workshop Cutoff</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Cutoff</span><a class="add mi-1 popup-btn" data-tab="addworkshopcutoff"><?php add() ?>Add Cutoff</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Cutoff Title</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <?php
                                                 $sql_cal	=	array();
                                                $sql_cal['QUERY']	=	"SELECT *  
                                                                        FROM " . _DB_WORKSHOP_CUTOFF_ . " 
                                                                    WHERE status != 'D'";
                                                $res_cal			=	$mycms->sql_select($sql_cal);
                                                $i = 1;

                                                foreach ($res_cal as $key => $rowsl) {
                                                ?>
                                                <td class="sl"><?= $i ?></td>
                                                <td><?= $rowsl['cutoff_title'] ?></td>
                                                <td><?= displayDateFormat($rowsl['start_date']) ?></td>
                                                <td><?= displayDateFormat($rowsl['end_date']) ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <?php	
                                                        if($rowsl['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?=($rowsl['status']=='A')?'InactiveWorkshop':'ActiveWorkshop'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?=($rowsl['status']=='A')?'InactiveWorkshop':'ActiveWorkshop'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                         <a href="javascript:void(0);" 
                                                            class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editWorkCutOffBtn" 
                                                            data-tab="editworkshopcutoff"
                                                            data-id="<?= $rowsl['id'] ?>"
                                                            data-title="<?= htmlspecialchars($rowsl['cutoff_title']) ?>"
                                                            data-end_date="<?= $rowsl['end_date'] ?>"
                                                            data-start_date="<?= $rowsl['start_date'] ?>"
                                                             data-status="<?= $rowsl['status'] ?>"
                                                         >
                                                            <?php edit(); ?>
                                                        </a>
                                                       <a href="javascript:void(0);" 
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                            onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                            window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=deleteWorkshopCutoff&id=<?= $rowsl['id'] ?>'; 
                                                                        }">
                                                                <?php delete(); ?>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?
                                                $i++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="com_info_box" id="registration">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php conregi() ?></span> Registration Classifications</n>
                                <a class="add mi-1 popup-btn" data-tab="newregistrationclassification"><?php add() ?>Add Classification</a>
                            </h5>
                            <div class="accm_listing">
                            <?php
                                    $sql_cal			=	array();
                                    $sql_cal['QUERY']	=	"SELECT * 
                                                                FROM " . _DB_REGISTRATION_CLASSIFICATION_ . "
                                                                WHERE `status` 	!= 		'D'
                                                                ORDER BY `sequence_by` ASC";


                                    $res_cal = $mycms->sql_select($sql_cal);

                                    $i = 1;

                                    foreach ($res_cal as $key => $rowsl) {

                                        $icon_image = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowsl['icon'];
                                    ?>
                                <div class="accm_box">
                                    <div class="accm_top">
                                        <div class="spot_name d-flex align-items-start">
                                            <div class="regi_img_circle">
                                                <img src="<?=$icon_image?>" alt="" class="w-100 h-100">
                                            </div>
                                            <div>
                                                <div class="regi_name"><?= $rowsl['classification_title'] ?> - <?= $rowsl['type'] ?></div>
                                            </div>
                                        </div>
                                        <div class="accm_details">
                                            <div class="accm_details_box">
                                                <h5>Seat Limits</h5>
                                                <h6><?= $rowsl['seat_limit'] ?></h6>

                                            </div>
                                            <div class="accm_details_box">
                                                <h5>Created Date</h5>
                                                <h6><?php calendar() ?><?= displayDateFormat($rowsl['created_dateTime']) ?></h6>
                                            </div>
                                            <div class="accm_details_box action" style="flex:unset;">
                                                <h5 class="text-right">Status</h5>
                                                <div class="action_div">
                                                     <?php	
                                                        if($rowsl['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="accm_bottom justify-content-end">
                                        <div class="spot_box_bottom_right">
                                            <a href="javascript:void(0);" 
                                                class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editRegisclassificationBtn" 
                                                data-tab="editRegistrationclassification"
                                                data-id="<?= $rowsl['id'] ?>"
                                                data-title="<?= htmlspecialchars($rowsl['classification_title']) ?>"
                                                data-type="<?= $rowsl['type'] ?>"
                                                data-seat_limit="<?= $rowsl['seat_limit'] ?>"
                                                data-sequence_by="<?= $rowsl['sequence_by'] ?>"
                                                data-currency="<?= $rowsl['currency'] ?>"
                                                data-inclusion_sci_hall="<?= $rowsl['inclusion_sci_hall'] ?>"
                                                data-inclusion_exb_area="<?= $rowsl['inclusion_exb_area'] ?>"
                                                data-inclusion_tea_coffee="<?= $rowsl['inclusion_tea_coffee'] ?>"
                                                data-status="<?= $rowsl['status'] ?>"
                                                ><?php edit(); ?>Edit</a>
                                            <a href="javascript:void(0);" 
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                            onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                            window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_process.php?act=Remove&amp;id=<?= $rowsl['id']; ?>'; 
                                                                        }"><?php delete(); ?>Delete</a>
                                            <a href="javascript:void(null)" class="drp icon_hover badge_dark action-transparent">Registration Tariff<i class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="accm_tariff spot_service_break">
                                        <div class="service_breakdown_wrap mt-0">
                                            <h4><?php rupee() ?>Tariff Breakdown</h4>
                                            <div class="table_wrap">
                                                 <? 
                                                    $sql = array();
                                                    $sql['QUERY']	=	"SELECT cutoff.cutoff_title  
                                                                        FROM "._DB_TARIFF_CUTOFF_." cutoff
                                                                        WHERE status = 'A'";
                                                    $res=$mycms->sql_select($sql);
                                                   			
                                                    ?>		
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>Registration Classification</th>
                                                            <?
                                                            foreach($res as $k=>$title)
                                                            {	
                                                            ?>
                                                            <th class="text-right"><?=strip_tags($title['cutoff_title'])?></th>
                                                            <?
                                                            }
                                                            ?>
                                                            <th class="action">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                         <?php
                                                        $sqlRegClasf			= array();
                                                        $sqlRegClasf['QUERY']	= "SELECT `classification_title`,`id`,`currency`,`type` ,`isOffer`,icon, residential_hotel_id
                                                                                    FROM "._DB_REGISTRATION_CLASSIFICATION_." 
                                                                                    WHERE status != ? 
                                                                                    AND `id` = ?".
                                                                                    (($reviewOperational)?" AND is_operational = 'Y' ":"")
                                                                            ." ORDER BY (CASE WHEN `type` = 'DELEGATE' THEN 1
                                                                                                WHEN `type`	= 'ACCOMPANY' THEN 2
                                                                                                ELSE 9999 END) ASC, sequence_by ASC";
                                                        $sqlRegClasf['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'D',  'TYP' => 's');	
                                                        $sqlRegClasf['PARAM'][]  = array('FILD' => 'id',  'DATA' =>$rowsl['id'],  'TYP' => 's');			
		
                                                        $resRegClasf			 = $mycms->sql_select($sqlRegClasf);				
                                                        if($resRegClasf)
                                                        {
                                                            foreach($resRegClasf as $key=>$registrationDetailsVal)
                                                            {
                                                                $sqlcutoff				= array();
                                                                $sqlcutoff['QUERY']		= "SELECT * FROM "._DB_TARIFF_CUTOFF_." WHERE status = ?";
                                                                $sqlcutoff['PARAM'][]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');						 
                                                                $rescutoff				= $mycms->sql_select($sqlcutoff);
                
                                                            
                                                            ?>
                                                        <tr>
                                                            <td><?=$registrationDetailsVal['classification_title']?></td>
                                                            <?
                                                            foreach($rescutoff as $keycutoff=>$cutoffvalue)
                                                            {
                                                                $sqlTarrif				= array();
                                                                $sqlTarrif['QUERY'] 	= "SELECT *
                                                                                            FROM "._DB_TARIFF_REGISTRATION_." 
                                                                                            WHERE tariff_classification_id = ?
                                                                                            AND tariff_cutoff_id = ?";
                                                                $sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_classification_id',  	'DATA' =>$registrationDetailsVal['id'],  'TYP' => 's');		
                                                                $sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',  			'DATA' =>$cutoffvalue['id'],  'TYP' => 's');		
                                                                $resTarrif				= $mycms->sql_select($sqlTarrif);
                                                                //   echo'<pre>';print_r($resTarrif);
                                                            ?>
                                                            <td class="text-right"><?=$registrationDetailsVal['currency']?> <?=$resTarrif[0]['amount']?></td>
                                                            <?php
                                                            }
                                                            ?>
                                                            <td class="action">
                                                                <div class="action_div">
                                                                    <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn editregistrationtariffbtn"  data-class-id="<?= $rowsl['id'] ?>" data-class-title="<?= htmlspecialchars($rowsl['classification_title']) ?>" data-tab="editregistrationtariff"><?php edit(); ?></a>
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
                                 <?
                                $i ++;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="com_info_box" id="workshop">
                    <div class="com_info_box_grid">
                        <!-- <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><?php workshop() ?></span> Workshop Type</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Workshop Type</span><a class="add mi-1 popup-btn" data-tab="addworkshoptype"><?php add() ?>Add Type</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Workshop Type</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="sl">1</td>
                                                <td>Normal</td>
                                                <td>
                                                    <div class="action_div">
                                                        <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="editworkshop"><?php edit(); ?></a>
                                                        <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> -->
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><?php workshop() ?></span> Workshops</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Workshop</span><a class="add mi-1 popup-btn" data-tab="addworkshop"><?php add() ?>Add Workshop</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Workshop</th>
                                                <th>Seat</th>
                                                <th>Venue</th>
                                                <th>Date</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php	
							                    $sql_cal	=	array();
                                                $sql_cal['QUERY']	=	"SELECT * 
                                                                        FROM "._DB_WORKSHOP_CLASSIFICATION_." 
                                                                        WHERE status != 'D' 
                                                                        ORDER BY `display` ASC";
                                                $res_cal=$mycms->sql_select($sql_cal);
                                                $i=1;
                                                
                                            foreach($res_cal as $key=>$rowsl)
                                            {
                                            ?>
                                            <tr>

                                                <td class="sl"><?=$i?></td>
                                                <td><?=$rowsl['classification_title']?> - <?=$rowsl['type']?></td>
                                                <td><?=$rowsl['seat_limit']?></td>
                                                <td><?=$rowsl['venue']?></td>
                                                <td><?=displayDateFormat($rowsl['workshop_date'])?></td>
                                                <td>
                                                    <div class="action_div">
                                                          <?php	
                                                        if($rowsl['status']=='A'){
                                                         ?>
                                                          <a href="https://ruedakolkata.com/natcon_25/conference_registration/webmaster/manage_workshop.process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="https://ruedakolkata.com/natcon_25/conference_registration/webmaster/manage_workshop.process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>

                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                      
                                                       <a href="javascript:void(0);" 
                                                            class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn editWorkshopBtn" 
                                                            data-tab="editworkshop"
                                                            data-id="<?= $rowsl['id'] ?>"
                                                            data-title="<?= htmlspecialchars($rowsl['classification_title']) ?>"
                                                            data-type="<?= $rowsl['type'] ?>"
                                                            data-seat="<?= $rowsl['seat_limit'] ?>"
                                                            data-venue="<?= htmlspecialchars($rowsl['venue']) ?>"
                                                            data-date="<?= $rowsl['workshop_date'] ?>"
                                                            data-status="<?= $rowsl['status'] ?>">
                                                            <?php edit(); ?>
                                                        </a>
                                                        <a href="javascript:void(0);" 
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                            onclick="if (confirm('Do you really want to remove this workshop?')) { 
                                                                            window.location.href='https://ruedakolkata.com/natcon_25/conference_registration/webmaster/manage_workshop.process.php?act=delete&id=<?= $rowsl['id'] ?>'; 
                                                                        }">
                                                                <?php delete(); ?>
                                                        </a>                                                    
                                                    </div>
                                                </td>
                                            </tr>
                                            <?
                                            $i++;		
                                             }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><?php credit() ?></span> Workshop Tariff</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Workshop Name</th>
                                                <th class="text-right">Workshop Tariff</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php	
                                                $sqlWorkshopclsf 	=	array();
                                                $sqlWorkshopclsf['QUERY'] = "SELECT `classification_title`,`id` 
                                                                            FROM "._DB_WORKSHOP_CLASSIFICATION_." 
                                                                            WHERE status != 'D' 
                                                                           ORDER BY `display` ASC";	
	                                            $resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
                                              
                                                foreach($resWorkshopclsf as $key=>$rowsl)
                                                {
                                            ?>
                                            <tr>
                                                
                                                <td><?=$rowsl['classification_title']?></td>
                                                <td>
                                                    <div class="action_div"><a class="badge_secondary br-5 wrkshp_trak w-auto">Workshop Tariff <g><?php down() ?></g></a></div>
                                                </td>
                                            </tr>
                                            <tr class="sub_table_tr">
                                                <td colspan="2">
                                                    <div class="table_wrap">
                                                        
                                                        <table>
                                                            <thead>
                                                                <tr>
                                                                    <th rowspan="2">Classification</th>
                                                                    	<?
                                                                        $sql	=	array();
                                                                        $sql['QUERY'] = "SELECT cutoff.cutoff_title,cutoff.id 
                                                                                        FROM "._DB_WORKSHOP_CUTOFF_." cutoff
                                                                                        WHERE status = ?";
                                                                        $sql['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');					 
                                                                        $res = $mycms->sql_select($sql);
                                                                        foreach($res as $key=>$title)
                                                                        {	
                                                                        ?>
                                                                            <th  colspan="2"><?=strip_tags($title['cutoff_title'])?></th>
                                                                            
                                                                        <?
                                                                        }
                                                                        ?>
                                                                    <th class="action" rowspan="2">Action</th>
                                                                </tr>
                                                                <tr>
                                                                    <?
                                                                    foreach($res as $key=>$title)
                                                                    {	
                                                                    ?>
                                                                    <th class="text-right">INR</th>
                                                                    <th class="text-right">USD</th>
                                                                  <?
                                                                    }
                                                                    ?>	
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?
                                                                $sql_cal	=	array();
                                                                $sql_cal['QUERY']		 =	"SELECT * 
                                                                                            FROM "._DB_REGISTRATION_CLASSIFICATION_." 
                                                                                            WHERE status != ? 
                                                                                        ORDER BY `sequence_by` ASC";
                                                                $sql_cal['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'D',  'TYP' => 's');							
                                                                $res_cal=$mycms->sql_select($sql_cal);	
                                               
                                                                foreach($res_cal as $keyRCf=>$rows2)
								                                {	
                                                                ?>
                                                                <tr>
                                                                    <td><?=$rows2['classification_title']?></td>
                                                                    <?php
                                                                     foreach($res as $keyCutoff => $title) {

                                                                    $sqlFetchTariffAmount = array();
                                                                    $sqlFetchTariffAmount['QUERY'] = "SELECT * 
                                                                                                    FROM "._DB_TARIFF_WORKSHOP_." 
                                                                                                    WHERE `workshop_id` = ? 
                                                                                                    AND `tariff_cutoff_id` = ?
                                                                                                    AND `registration_classification_id` = ?";
                                                                    $sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'workshop_id', 'DATA'=>$rowsl['id'], 'TYP'=>'s');
                                                                    $sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'tariff_cutoff_id', 'DATA'=>$title['id'], 'TYP'=>'s');
                                                                    $sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'registration_classification_id', 'DATA'=>$rows2['id'], 'TYP'=>'s');

                                                                    $resultFetchTariffAmount = $mycms->sql_select($sqlFetchTariffAmount);

                                                                    if (!empty($resultFetchTariffAmount)) {
                                                                        $rowsTariffAmount = $resultFetchTariffAmount[0];
                                                                    ?>
                                                                            <td class="text-right"><?=number_format($rowsTariffAmount['inr_amount'] ?? 0, 2)?></td>
                                                                            <td class="text-right"><?=number_format($rowsTariffAmount['usd_amount'] ?? 0, 2)?></td>
                                                                    <?
                                                                        } else { ?>
                                                                            <td class="text-right">0.00</td>
                                                                            <td class="text-right">0.00</td>
                                                                    <?
                                                                        }
                                                                    } // end cutoff loop
                                                                    ?>
                                                                    <td class="action">
                                                                        <div class="action_div">
                                                                            <a href="#"
                                                                                class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn editTariffBtn"
                                                                                data-tab="editworkshoptariff"

                                                                                data-workshop-id="<?=$rowsl['id']?>"
                                                                                data-workshop-title="<?=$rowsl['classification_title']?>"
                                                                                data-classification="<?=$rows2['classification_title']?>"
                                                                                data-classification-id="<?=$rows2['id']?>"
                                                                                data-classification-currency="<?=$rows2['currency']?>"
                                                                                >
                                                                                <?php edit(); ?>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?
                                                                }
                                                                ?>
                                                           
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="accompany">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><?php duser() ?></span> Accompany</n>
                                <a class="add mi-1 popup-btn" data-tab="addaccompany"><?php add() ?>Add Classification</a>
                            </h5>
                           
                            <div class="accm_listing">
                                <?php
                                    $sql_cal			=	array();
                                    $sql_cal['QUERY']	=	"SELECT * 
                                                                FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . "
                                                                WHERE `status` 	!= 		?
                                                                ORDER BY `id` ASC";

                                    $sql_cal['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');

                                    $res_cal = $mycms->sql_select($sql_cal);

                                    $i = 1;

                                    foreach ($res_cal as $key => $rowsl) {
                                    ?>
                                <div class="accm_box">
                                    <div class="accm_top">
                                        <div class="spot_name">

                                            <div>
                                                <div class="regi_name"><?= $rowsl['classification_title'] ?> - <?= $rowsl['type'] ?></div>

                                            </div>
                                        </div>
                                        <div class="accm_details">
                                            <div class="accm_details_box">
                                                <h5>Created Date</h5>
                                                <h6><?php calendar() ?><?= displayDateFormat($rowsl['created_dateTime']) ?></h6>
                                            </div>
                                            <div class="accm_details_box action" style="flex:unset;">
                                                <h5 class="text-right">Status</h5>
                                                <div class="action_div">
                                                    <?php	
                                                        if($rowsl['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="accm_bottom justify-content-end">
                                        <div class="spot_box_bottom_right">
                                            <a href="javascript:void(null)" class="popup-btn icon_hover badge_secondary action-transparent editaccompanyBtn" data-tab="editaccompany"  data-id="<?=$rowsl['id'];?>"><?php edit(); ?>Edit</a>
                                            <a href="javascript:void(0);" 
                                                class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php?act=Remove&id=<?= $rowsl['id'] ?>'; 
                                                            }"><?php delete(); ?>Delete</a>   
                                             <a href="javascript:void(null)" class="drp icon_hover badge_dark action-transparent">Accompany Tariff<i class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="accm_tariff spot_service_break">
                                        <div class="service_breakdown_wrap mt-0">
                                            <h4><?php rupee() ?>Tariff Breakdown</h4>
                                            <div class="table_wrap">
                                                <? 
						                            $sql = array();
                                                    $sql['QUERY']	=	"SELECT cutoff.cutoff_title,`id`  
                                                                        FROM "._DB_TARIFF_CUTOFF_." cutoff
                                                                        WHERE status = 'A'";
                                                    $res=$mycms->sql_select($sql);
                                                    
                                                    // $registrationDetails = getAllAccompanyTariffs();						
                                                    // echo'<pre>';print_r($registrationDetails);echo'</pre>';						
                                                    ?>		
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th class="sl">#</th>
                                                            <th>Accompany Classification</th>
                                                            <?

                                                            foreach($res as $k=>$title)
                                                            {	
                                                            ?>
                                                            <th class="text-right" ><?=strip_tags($title['cutoff_title'])?></th>
                                                            <?
                                                            }
                                                            ?>
                                                            <th class="action" >Action</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="sl">1</td>
                                                            <td><?= $rowsl['classification_title'] ?> - <?= $rowsl['type'] ?></td>
                                                            <?
                                                            foreach($res as $k=>$title)
                                                            {	
                                                                $sqlTarrif				= array();
                                                                $sqlTarrif['QUERY'] 	= "SELECT *
                                                                                            FROM "._DB_TARIFF_ACCOMPANY_." 
                                                                                            WHERE tariff_classification_id = ?
                                                                                            AND tariff_cutoff_id = ?";
                                                                $sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_classification_id',  	'DATA' =>$rowsl['id'],  'TYP' => 's');		
                                                                $sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',  			'DATA' =>$title['id'],  'TYP' => 's');		
                                                                $resTarrif				= $mycms->sql_select($sqlTarrif);
                                                            ?>
                                                            <td class="text-right"><?=number_format($resTarrif[0]['amount'],2)??'0.00'?></td>
                                                             <?
                                                            }
                                                            ?>
                                                            <td class="action">
                                                                <div class="action_div">
                                                                    <a class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn editaccompanytariffbtn" data-tab="editaccompanytariff"  data-title="<?= $rowsl['classification_title'] ?> - <?= $rowsl['type'] ?>" data-id="<?=$rowsl['id']?>"><i class="fal fa-pencil"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?
                                    }
                                    ?>
                            </div>
                              <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Accompany Classification</span><a class="add mi-1 popup-btn" data-tab="addaccompany"><?php add() ?>Add Classification</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Manage Input Field for Accompany</th>
                                                <th class="action">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Food Preference</td>
                                                <td class="action">
                                                    <?
                                                    $sql_cal			=	array();
                                                    $sql_cal['QUERY']	=	"SELECT `food_preference`,`id` 
                                                                                FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . "
                                                                                WHERE `status` 	!= 		?
                                                                                ORDER BY `id` ASC";

                                                    $sql_cal['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');

                                                    $res_cal = $mycms->sql_select($sql_cal);
                                                    ?>
                                                    <div class="action_div">
                                                        <?
                                                        if($res_cal[0]['food_preference']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php?act=<?=($res_cal[0]['food_preference']=='A')?'foodPrefInactive':'foodPrefActive'?>&id=<?=$res_cal[0]['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php?act=<?=($res_cal[0]['food_preference']=='A')?'foodPrefInactive':'foodPrefActive'?>&id=<?=$res_cal[0]['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                     
                    </div>
                </div>
                <div class="com_info_box" id="dinner">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><?php dinner() ?></span> Dinner</n>
                                <a class="add mi-1 popup-btn" data-tab="adddinner"><?php add() ?>Add Dinner</a>
                            </h5>
                            <div class="accm_listing">
                                 <? 
                                    $sqlFetchDinner				=	array();
                                    $sqlFetchDinner['QUERY']    = "SELECT * 
                                                                    FROM "._DB_DINNER_CLASSIFICATION_."
                                                                    WHERE `status` != ?";
                                    $sqlFetchDinner['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'D' , 		'TYP' => 's');
                                    $resultFetchDinner    		= $mycms->sql_select($sqlFetchDinner);
                                    $dinnerCounter	=	1;
                                    if($resultFetchDinner){
                                ?>
                                <? foreach($resultFetchDinner as $key=>$rowFetchDinner ){ ?>	
                                <div class="accm_box">
                                    <div class="accm_top">
                                        <div class="spot_name">
                                            <div>
                                                <div class="regi_name"><?=$rowFetchDinner['dinner_classification_name']?></div>
                                                <div class="regi_contact">
                                                    <span>
                                                        <?php calendar() ?><?=$rowFetchDinner['date']?>
                                                    </span>
                                                    <span>
                                                        <?php address() ?><?=$rowFetchDinner['dinner_hotel_name']?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accm_details">
                                             <div class="accm_details_box">
                                                <h5>Link</h5>
                                                <h6><a href="<?=$rowFetchDinner['link']?>" target="_blank">Direction</a></h6>
                                            </div>
                                            <div class="accm_details_box action" style="flex:unset;">
                                                <h5 class="text-right">Status</h5>
                                                <div class="action_div">
                                                     <?php	
                                                        if($rowFetchDinner['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>dinner_classificaton.process.php?act=<?=($rowFetchDinner['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFetchDinner['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>dinner_classificaton.process.php?act=<?=($rowFetchDinner['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFetchDinner['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="accm_bottom justify-content-end">
                                        <div class="spot_box_bottom_right">
                                          <a href="javascript:void(0);" 
                                            class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editdinnerBtn" 
                                            data-tab="editdinner"
                                            data-id="<?= $rowFetchDinner['id'] ?>"
                                            data-dinner_classification_name="<?= htmlspecialchars($rowFetchDinner['dinner_classification_name'])?>"
                                            data-date="<?= $rowFetchDinner['date'] ?>"
                                            data-dinner_hotel_name="<?= $rowFetchDinner['dinner_hotel_name'] ?>"
                                            data-link="<?= $rowFetchDinner['link'] ?>"
                                            ><?php edit(); ?>Edit</a>
                                            <a href="javascript:void(0);" 
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                            onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                            window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>dinner_classificaton.process.php?act=Remove&id=<?= $rowFetchDinner['id'] ?>'; 
                                                                        }"><?php delete(); ?>Delete</a>
                                            <a href="javascript:void(null)" class="drp icon_hover badge_dark action-transparent">Dinner Tariff<i class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="accm_tariff spot_service_break">
                                        <div class="service_breakdown_wrap mt-0">
                                            <h4><?php rupee() ?>Tariff Breakdown</h4>
                                            <div class="table_wrap">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th class="sl" rowspan="2">#</th>
                                                            <th rowspan="2">Dinner</th>
                                                            <?
                                                                $sqlcutoff	=	array();
                                                                $sqlcutoff['QUERY'] = "SELECT * FROM "._DB_TARIFF_CUTOFF_." cutof WHERE status = ?" ;
                                                                $sqlcutoff['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'A' , 		'TYP' => 's');
                                                                $resCutoff = $mycms->sql_select($sqlcutoff);	
                                                                foreach($resCutoff as $keyCutoff=> $rowCutoff)
		                                                         	{	
                                                                ?>
                                                            <th class="text-right" colspan="2"><?=$rowCutoff['cutoff_title']?></th>
                                                            <?php
                                                            }
                                                            ?>
                                                            <th class="action" rowspan="2">Action</th>
                                                        </tr>
                                                        <tr>
                                                             <?
                                                            foreach($resCutoff as $keyCutoff=> $rowCutoff)
		                                                         	{	
                                                                ?>
                                                            <th class="text-right">INR</th>
                                                            <th class="text-right">USD</th>
                                                           <?php
                                                            }
                                                            ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="sl">1</td>
                                                            <td><?=$rowFetchDinner['dinner_classification_name']?></td>
                                                            <?
                                                            foreach($resCutoff as $keyCutoff=> $rowCutoff)
		                                                        {	
                                                                    $sqlPackageCheckoutDate	=	array();
                                                                    // query in tariff table
                                                                    $sqlPackageCheckoutDate['QUERY'] = "select * 
                                                                                                        FROM "._DB_DINNER_TARIFF_." accomodation
                                                                                                        WHERE status = ?
                                                                                                        AND cutoff_id = ?
                                                                                                        AND dinner_classification_id = ?
                                                                                                        AND status = ?";

                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 					 'DATA' => 'A' , 					'TYP' => 's');
                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'cutoff_id' ,				 'DATA' => $rowCutoff['id'] , 		'TYP' => 's');
                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'dinner_classification_id' , 'DATA' => $rowFetchDinner['id'] , 		'TYP' => 's');				   
                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 					 'DATA' => 'A' , 		'TYP' => 's');				   
                                                                    $resPackageCheckoutDate = $mycms->sql_select($sqlPackageCheckoutDate);
                                                                ?>
                                                            <td class="text-right"><?=number_format($resPackageCheckoutDate[0]['inr_amount'],2)??'0.00'?></td>
                                                            <td class="text-right"><?=number_format($resPackageCheckoutDate[0]['usd_amount'],2)??'0.00'?></td>
                                                            <?php
                                                            }
                                                            ?>
                                                            <td class="action">
                                                                <div class="action_div">
                                                                    <a class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn editDinnertariffbtn" data-tab="editDinnertariff" data-id="<?=$rowFetchDinner['id']?>"><i class="fal fa-pencil"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 <?
                                            
                                    }
                                    $dinnerCounter++;
                                    }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
                   <div class="com_info_box" id="dinnerOld">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><?php dinner() ?></span> Dinner</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Dinner</span><a class="add mi-1 popup-btn" data-tab="adddinner"><?php add() ?>Add Dinner</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Dinner Classification</th>
                                                <th>Date</th>
                                                <th>Hotel</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <? 
                                                $sqlFetchDinner				=	array();
                                                $sqlFetchDinner['QUERY']    = "SELECT * 
                                                                                FROM "._DB_DINNER_CLASSIFICATION_."
                                                                                WHERE `status` != ?";
                                                $sqlFetchDinner['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'D' , 		'TYP' => 's');
                                                $resultFetchDinner    		= $mycms->sql_select($sqlFetchDinner);
                                                $dinnerCounter	=	1;
                                                if($resultFetchDinner){
                                            ?>
                                            <? foreach($resultFetchDinner as $key=>$rowFetchDinner ){ ?>	
                                            <tr>
                                                <td class="sl"><?=$dinnerCounter?></td>
                                                <td><span><?=$rowFetchDinner['dinner_classification_name']?></span>
                                                    <div class="regi_contact">
                                                        <span>
                                                            <a href="<?= htmlspecialchars($rowFetchDinner['link']) ?>" target="_blank">
                                                                <i class="fal fa-globe"></i>
                                                            </a>                                                        
                                                        </span>
                                                    </div>
                                                </td>
                                                <td><?=$rowFetchDinner['date']?></td>
                                                <td><?=$rowFetchDinner['dinner_hotel_name']?></td>
                                                <td>
                                                    <div class="action_div">
                                                         <?php	
                                                        if($rowFetchDinner['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>dinner_classificaton.process.php?act=<?=($rowFetchDinner['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFetchDinner['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>dinner_classificaton.process.php?act=<?=($rowFetchDinner['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFetchDinner['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>

                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="javascript:void(0);" 
                                                            class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editdinnerBtn" 
                                                            data-tab="editdinner"
                                                            data-id="<?= $rowFetchDinner['id'] ?>"
                                                            data-dinner_classification_name="<?= htmlspecialchars($rowFetchDinner['dinner_classification_name'])?>"
                                                            data-date="<?= $rowFetchDinner['date'] ?>"
                                                            data-dinner_hotel_name="<?= $rowFetchDinner['dinner_hotel_name'] ?>"
                                                            data-link="<?= $rowFetchDinner['link'] ?>"
                                                         >
                                                            <?php edit(); ?>
                                                        </a>
                                                        <a href="javascript:void(0);" 
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                            onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                            window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>dinner_classificaton.process.php?act=Remove&id=<?= $rowFetchDinner['id'] ?>'; 
                                                                        }">
                                                                <?php delete(); ?>
                                                        </a>                                                    
                                                    </div>
                                                </td>
                                            </tr>
                                            <?
                                            
                                                }
                                                $dinnerCounter++;
                                              }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="com_info_box" id="accommodation">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_danger"><?php hotel() ?></span> Accommodation</n>
                                <a class="add mi-1 popup-btn" data-tab="newhotel"><?php add(); ?>Add Hotel</a>

                            </h5>
                            <div class="accm_listing">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Accommodation</span><a class="add mi-1 popup-btn" data-tab="newhotel"><?php add(); ?>Add Hotel</a></h4> -->
                                <div class="accm_box">
                                    <?php
                                        $sqlFetchHotel				=	array();
                                        $sqlFetchHotel['QUERY']		=	"SELECT * 
                                                                        FROM " . _DB_MASTER_HOTEL_ . "
                                                                        WHERE `status` 		!= 	 ? ";

                                        $sqlFetchHotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');
                                        $resultFetchHotel    		= $mycms->sql_select($sqlFetchHotel);
                                        if ($resultFetchHotel) {
                                            foreach ($resultFetchHotel as $keyHotel => $rowFetchHotel) {
                                            $hotel_id 			    = $rowFetchHotel['id'];

                                    ?>
                                    <div class="accm_top">
                                        <div class="accm_name">
                                            <div class="regi_name"><?=$rowFetchHotel['hotel_name']?></div>
                                            <!-- <div class="regi_type">
                                                <span class="badge_padding badge_default">Package 1</span>
                                                <span class="badge_padding badge_default">Package 2</span>
                                            </div> -->
                                            <?php
                                                $sql 		    	  =	array();
                                                $sql['QUERY'] = "SELECT min(`check_in_date`) AS checkin								
                                                                FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
                                                                WHERE hotel_id = ?
                                                                    AND status != ?";
                                                $sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $hotel_id, 'TYP' => 's');
                                                $sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
                                                $result = $mycms->sql_select($sql);
                                                $rowFetchcheckIn           = $result[0];

                                                $sql 		    	  =	array();
                                                $sql['QUERY'] = "SELECT max(`check_out_date`) AS checkout								
                                                                FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
                                                                WHERE hotel_id = ?
                                                                    AND status !=?";

                                                $sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $hotel_id, 'TYP' => 's');
                                                $sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
                                                $result = $mycms->sql_select($sql);
                                                $rowFetchcheckOut           = $result[0];

                                            ?>
                                            <div class="regi_contact">
                                                <span>
                                                    <?php calendar() ?><?= date('d-m-Y',strtotime($rowFetchcheckIn['checkin'])) ?><i class="fal fa-long-arrow-right ml-1 mr-1"></i><?= date('d-m-Y',strtotime($rowFetchcheckOut['checkout'] ))?>
                                                </span>
                                                <span>
                                                    <?php call() ?><?= $rowFetchHotel['hotel_phone_no'] ?>
                                                </span>
                                                <span>
                                                    <?php address() ?><?= $rowFetchHotel['hotel_address'] ?>
                                                </span>
                                                <span>
                                                    <?php address() ?><?= $rowFetchHotel['distance_from_venue'] ?> km from Venue
                                                </span>
                                            </div>
                                        </div>
                                        <div class="accm_details">
                                            <div class="accm_details_box">
                                                <h5>Seat Limits</h5>
                                                <ul class="accm_ul aminity_ul">
                                                    <?php
                                                        $sql 		    	  =	array();
                                                        $sql['QUERY'] = "SELECT `check_in_date`,`seat_limit`								
                                                                                    FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
                                                                                    WHERE hotel_id = ?
                                                                                        AND status != ?";
                                                        $sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $hotel_id, 'TYP' => 's');
                                                        $sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
                                                        $result = $mycms->sql_select($sql);
                                                        // $rowSeatLimit          = $result[0];
                                                        foreach ($result as $key => $rowSeatLimit) {
                                                    ?>
                                                    <li><?php calendar() ?>
                                                        <n class="d-inline-block"><?= date('d-m-Y',strtotime($rowSeatLimit['check_in_date'])) ?></n>
                                                        <i class="fal fa-long-arrow-right"></i>
                                                        <n class="d-inline-block"><?= $rowSeatLimit['seat_limit']?></n>
                                                    </li>
                                                    <?php
                                                        }
                                                    ?>
                                                </ul>

                                            </div>
                                            <div class="accm_details_box action" style="flex:unset;">
                                                <h5 class="text-right">Status</h5>
                                                <div class="action_div">
                                                    <?php	
                                                        if($rowFetchHotel['status']=='A'){
                                                        ?>
                                                        <a href="hotel_listing.process.php?act=<?=($rowFetchHotel['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFetchHotel['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                    <?php	
                                                    }else{
                                                    ?>
                                                        <a href="hotel_listing.process.php?act=<?=($rowFetchHotel['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFetchHotel['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                    <?php	
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="accm_bottom justify-content-end">
                                        <div class="spot_box_bottom_right">
                                            <a href="javascript:void(null)" class="popup-btn icon_hover badge_primary action-transparent viewHotalBtn" data-tab="viewhotel"  data-id="<?= $hotel_id ?>"><?php view(); ?>View</a>
                                            <a href="javascript:void(null)" class="popup-btn icon_hover badge_secondary action-transparent hotelEditBtn" data-tab="edithotel" data-hotel_id="<?=$rowFetchHotel['id']?>"><?php edit(); ?>Edit</a>
                                            <a href="#" class="icon_hover badge_danger action-transparent" onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                            window.location.href='hotel_listing.process.php?act=Remove&id=<?= $rowFetchHotel['id'] ?>'; 
                                                                        }"><?php delete(); ?>Delete</a>
                                            <a href="#" class="drp icon_hover badge_dark action-transparent">Accommodation Tariff<i class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                     
                                    <div class="accm_tariff spot_service_break">
                                        <div class="service_breakdown_wrap mt-0">
                                            <h4><?php rupee() ?>Tariff Breakdown</h4>
                                            <div class="table_wrap">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th class="sl" rowspan="2">#</th>
                                                            <th rowspan="2">Room Type</th>
                                                            <th rowspan="2">Packages</th>
                                                            <? 
                
                                                            $sql 	=	array();
                                                            $sql['QUERY']	=	"SELECT id, cutoff.cutoff_title  
                                                                                    FROM "._DB_TARIFF_CUTOFF_." cutoff
                                                                                WHERE status = ?";
                                                            $sql['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'A' , 		'TYP' => 's');					   
                                                            $resCutoff=$mycms->sql_select($sql);
                                                            $cutOffsArray = array();
                                                            $cutOffCount = count($resCutoff);

                                                            foreach($resCutoff as $key=>$title)
                                                            {	
                                                            ?>	
                                                            <th class="text-right" colspan="2"><?=strip_tags($title['cutoff_title'])?></th>
                                                            <?
                                                            }
                                                            ?>
                                                            <th class="action" rowspan="2">Action</th>
                                                        </tr>
                                                       
                                                         
                                                        <tr>
                                                             <?
                                                               foreach($resCutoff as $key=>$title)
                                                            {	
                                                            ?>	
                                                            <th class="text-right">INR</th>
                                                            <th class="text-right">USD</th>
                                                           <?
                                                            }
                                                            ?>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        // Fetch rooms for the hotel
                                                        $sqlRoom = array();
                                                        $sqlRoom['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  
                                                                            WHERE hotel_id = ? AND status='A' AND purpose='room' ORDER BY id ASC";
                                                        $sqlRoom['PARAM'][] = array('DATA' => $hotel_id, 'TYP' => 's');
                                                        $rooms = $mycms->sql_select($sqlRoom);

                                                        $sl = 1;

                                                        if ($rooms) {

                                                            foreach ($rooms as $room) {

                                                                // Fetch packages for the hotel
                                                                $sqlFetchPack = array();
                                                                $sqlFetchPack['QUERY'] = "SELECT id, package_name 
                                                                                        FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                                                                        WHERE status = ? AND hotel_id = ?";
                                                                $sqlFetchPack['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                                $sqlFetchPack['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's');

                                                                $packages = $mycms->sql_select($sqlFetchPack);

                                                                $pkgCount = count($packages);

                                                                if ($packages) {

                                                                    foreach ($packages as $pIndex => $package) {
                                                                     
                                                        ?>
                                                        <tr>
                                                            <?php if ($pIndex === 0) { ?>
                                                                <td class="sl" rowspan="<?= $pkgCount ?>"><?= $sl++ ?></td>
                                                                <td rowspan="<?= $pkgCount ?>"><?= $room['accessories_name'] ?></td>
                                                            <?php } ?>

                                                            <td><?= $package['package_name'] ?></td>
                                                             <?
                                                              foreach($resCutoff as $key=>$title){	
                                                                   $sqlPackageCheckoutDate	=	array();
                                                                    // query in tariff table
                                                                    $sqlPackageCheckoutDate['QUERY'] = "select * 
                                                                                                        FROM "._DB_TARIFF_ACCOMMODATION_." 
                                                                                                        WHERE status = ?
                                                                                                        AND tariff_cutoff_id = ?
                                                                                                        AND hotel_id = ?
                                                                                                        AND roomTypeId = ?
                                                                                                        AND package_id = ?
                                                                                                        ";
                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 					'TYP' => 's');
                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' ,'DATA' => $title['id'] , 		'TYP' => 's');
                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotel_id, 		'TYP' => 's');
                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'roomTypeId' , 		'DATA' =>$room['id'], 		'TYP' => 's');	
                                                                    $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'package_id' , 		'DATA' =>$package['id'], 		'TYP' => 's');	
                                                                  
                                                                    $resPackageCheckoutDate = $mycms->sql_select($sqlPackageCheckoutDate);
                                                                   
                                                            
                                                            ?>	
                                                            <td class="text-right"><?= $resPackageCheckoutDate[0]['inr_amount']?? '0.00'?></td>
                                                            <td class="text-right"><?= $resPackageCheckoutDate[0]['usd_amount']?? '0.00'?></td>
                                                         
                                                            <?
                                                            }
                                                            ?>
                                                            <!-- Action icon per package -->
                                                             <?php if ($pIndex === 0) { // Only show once per room ?>
                                                                <td class="action" rowspan="<?= $pkgCount ?>">
                                                                    <div class="action_div">
                                                                        <a href="#"
                                                                        class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn accoEditbtn"
                                                                        data-tab="hoteltariff"
                                                                        data-room-id="<?= $room['id'] ?>"
                                                                        data-hotel-id ="<?= $hotel_id ?>"
                                                                        data-hotel ="<?= $rowFetchHotel['hotel_name']?>"
                                                                        data-room ="<?= $room['accessories_name'] ?>"
                                                                        data-package=""> <!-- Optional: remove package data -->
                                                                            <i class="fal fa-pencil"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                                <?php } ?>
                                                        </tr>
                                                        <?php
                                                                    } // end foreach package
                                                                } // end if packages
                                                            } // end foreach room
                                                        } // end if rooms
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <?
                                  }
                                   }
                                ?>
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
    $(document).on('click', '.editWorkshopBtn', function() {
        var id     = $(this).data('id');
        var title  = $(this).data('title');
        var type   = $(this).data('type');
        var venue  = $(this).data('venue');
        var seat   = $(this).data('seat');
        var date   = $(this).data('date');
        var status = $(this).data('status');

        $('#workshop_id').val(id);
        $('#workshop_Edit').val(title);
        $('#workshop_type').val(type);
        $('#venueEdit').val(venue);
        $('#seat_limit_Edit').val(seat);
        $('#workshop_date_Edit').val(date);

        if(status === 'A') {
            $('#edit_status_active').prop('checked', true);
        } else {
            $('#edit_status_inactive').prop('checked', true);
        }

        // Trigger the popup-btn functionality
    $('#editworkshop').fadeIn(); // or your popup open function
    });
     $(document).on('click', '.editCutOffBtn', function() {
        var id     = $(this).data('id');
        var title  = $(this).data('title');
        var end_date   = $(this).data('end_date');
        var start_date  = $(this).data('start_date');
        var status = $(this).data('status');


        $('#cutoff_id').val(id);
        $('#cutoff_title').val(title);
        $('#start_date').val(start_date);
        $('#end_date').val(end_date);

        // if(status === 'A') {
        //     $('#edit_status_active').prop('checked', true);
        // } else {
        //     $('#edit_status_inactive').prop('checked', true);
        // }

        // Trigger the popup-btn functionality
    $('#editregistrationcutoff').fadeIn(); // or your popup open function
    });

    //////////////////dinner edit//////////
      $(document).on('click', '.editdinnerBtn', function() {
        
        var id     = $(this).data('id');
        var classification_name  = $(this).data('dinner_classification_name');
        var date   = $(this).data('date');
        var dinner_hotel_name  = $(this).data('dinner_hotel_name');
        var link = $(this).data('link');


        $('#classification_id').val(id);
        $('#classification_name').val(classification_name);
        $('#date').val(date);
        $('#dinner_hotel_name').val(dinner_hotel_name);
        $('#link').val(link);

        console.log(link);

        // Trigger the popup-btn functionality
    $('#editdinner').fadeIn(); // or your popup open function
    });
    ////////////////////////////////

     $(document).on('click', '.editWorkCutOffBtn', function() {
        var id     = $(this).data('id');
        var title  = $(this).data('title');
        var end_date   = $(this).data('end_date');
        var start_date  = $(this).data('start_date');
        var status = $(this).data('status');


        $('#cutoff_id_workshp').val(id);
        $('#cutoff_title_workshp').val(title);
        $('#start_date_workshp').val(start_date);
        $('#end_date_workshp').val(end_date);

        // if(status === 'A') {
        //     $('#edit_status_active').prop('checked', true);
        // } else {
        //     $('#edit_status_inactive').prop('checked', true);
        // }

        // Trigger the popup-btn functionality
    $('#editworkshopcutoff').fadeIn(); // or your popup open function
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
    $(document).on('click', '.editTariffBtn', function () {

        let workshopId   = $(this).data('workshop-id');
        let title        = $(this).data('workshop-title');
        let classification = $(this).data('classification');
        let classificationId = $(this).data('classification-id');
        let currency = $(this).data('classification-currency');

        // Fill popup text
        // $('#workshop_classification_id').val(workshopId);
        // $('#registration_classification_id').val(classificationId);
        // $('.currencyClass').val(currency);

        // // Optional hidden input
        // $('#workshopClassTitle').text(title);
        // $('#regisClassTitle').text(classification);
       // Call PHP via AJAX
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                workshop_id: workshopId,
                registration_classification_id: classificationId,
                 title: title,
                classification: classification,
                currency1: currency,
            },
             success: function(response) {
                console.log(workshopId);
                console.log(classification);

                  $('#editworkshoptariff').html($(response).find('#editworkshoptariff').html());

        // Then show the popup
        $('#editworkshoptariff').fadeIn();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Open popup (if not already)
    });

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
     $(document).on('click', '.viewHotalBtn', function() {
        let hotelId   = $(this).data('id');
      
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                hotelId: hotelId,
              
            },
             success: function(response) {
                console.log(hotelId);


                  $('#viewhotel').html($(response).find('#viewhotel').html());

        // Then show the popup
        $('#viewhotel').fadeIn();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
    //////////////////////////////////////////////
        $(document).on('click', '.accoEditbtn', function() {
            //    console.log($(this).data('package'));
        let roomId   = $(this).data('room-id');
        let hotelTarrifId   = $(this).data('hotel-id');
        let roomName   = $(this).data('room');
        let HotelName   = $(this).data('hotel');
        $('#roomName').val(roomName);
        $('#HotelName').val(HotelName);
      
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                roomId: roomId,
                hotelTarrifId: hotelTarrifId,
               
            },
             success: function(response) {
                console.log(roomId);
                console.log(hotelTarrifId);


        $('#hoteltariff').html($(response).find('#hoteltariff').html());

        // Then show the popup
        $('#hoteltariff').fadeIn();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
      ///////////////////hotelEdit///////////////////////////
   $(document).on('click', '.hotelEditBtn', function () {

    let editHotelId = $(this).data('hotel_id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { editHotelId: editHotelId },
        success: function (response) {

            $('#edithotel').html($(response).find('#edithotel').html());

            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditHotelPackages();
            initEditHotelAminity();
            initEditHotelRoomType();
            initEditHotelSlider();

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
//////////////////registration classification edit///////////////
     $(document).on('click', '.editRegisclassificationBtn', function() {
            //    console.log($(this).data('package'));
        let classificationId   = $(this).data('id');
        let title   = $(this).data('title');
        let type   = $(this).data('type');
        let seat_limit   = $(this).data('seat_limit');
        let sequence_by   = $(this).data('sequence_by');
        let currency   = $(this).data('currency');
        let inclusion_sci_hall   = $(this).data('inclusion_sci_hall');
        let inclusion_exb_area   = $(this).data('inclusion_exb_area');
        let inclusion_tea_coffee   = $(this).data('inclusion_tea_coffee');

        // $('#roomName').val(roomName);
        // $('#HotelName').val(HotelName);
       if(inclusion_sci_hall === 'Y') {
            $('#inclusion_sci_hall').prop('checked', true);
        } else {
            $('#inclusion_sci_hall_inactive').prop('checked', true);
        }
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                classificationId: classificationId
               
            },
             success: function(response) {
                console.log(classificationId);


        $('#editRegistrationclassification').html($(response).find('#editRegistrationclassification').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditregClass();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
      ///////////////////registration tariff classification edit end///////////////////////////
       $(document).on('click', '.editregistrationtariffbtn', function() {
            //    console.log($(this).data('package'));
        let classId   = $(this).data('class-id');
        let classtitle   = $(this).data('class-title');
       
        $('#classtitle').text(classtitle);
        // $('#HotelName').val(HotelName);
       
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                classId: classId
               
            },
             success: function(response) {
                console.log(classId);


        $('#editregistrationtariff').html($(response).find('#editregistrationtariff').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditregClass();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
         ///////////////////registration tariff classification edit end///////////////////////////
       $(document).on('click', '.editDinnertariffbtn', function() {
            //    console.log($(this).data('package'));
        let dinnerId   = $(this).data('id');
       
       
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                dinnerId: dinnerId
               
            },
             success: function(response) {
                console.log(dinnerId);


        $('#editDinnertariff').html($(response).find('#editDinnertariff').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditregClass();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
    //////////////////accompany classification edit///////////////
     $(document).on('click', '.editaccompanyBtn', function() {
            //    console.log($(this).data('package'));
        let accompanyId   = $(this).data('id');
    
        // $('#roomName').val(roomName);
        // $('#HotelName').val(HotelName);
       
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                accompanyId: accompanyId
               
            },
             success: function(response) {
                console.log(accompanyId);


        $('#editaccompany').html($(response).find('#editaccompany').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditregClass();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
      ///////////////////accompany  edit end///////////////////////////
       $(document).on('click', '.editaccompanytariffbtn', function() {
            //    console.log($(this).data('package'));
        let accompanytariffId   = $(this).data('id');
        let accompanytarifftitle   = $(this).data('title');
       
        $('#accompanytitle').text(accompanytitle);
        // $('#HotelName').val(HotelName);
       
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                accompanytariffId: accompanytariffId
               
            },
             success: function(response) {
                console.log(accompanytariffId);


        $('#editaccompanytariff').html($(response).find('#editaccompanytariff').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditregClass();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
         ///////////////////accompany tariff  edit end///////////////////////////
</script>

</html>