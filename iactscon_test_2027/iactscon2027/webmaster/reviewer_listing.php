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
	
$pageKey                       		       = "_pgn_";
	$pageKeyVal                    		       = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString                 		       = "";
	$searchArray                   		       = array();
	
	$searchArray[$pageKey]         		       = $pageKeyVal;
	$searchArray['src_faculty_username']       = trim($_REQUEST['src_faculty_username']);
	$searchArray['src_faculty_name']           = trim($_REQUEST['src_faculty_name']);
	$searchArray['src_faculty_access_role']    = trim($_REQUEST['src_faculty_access_role']);
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
?>
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
                        <li class="breadcrumb-item active" aria-current="page">Reviewer Listing</li>
                    </ol>
                </nav>
                <h2>Reviewer Listing</h2>
                <h6>Issue ID cards, kits, and manage on-spot requests.</h6>
                  <a href="<?= _BASE_URL_ ?>faculty/section_login/login.php"  target="_blank" style="font-size: 12px;!important">Login URL : <?= _BASE_URL_ ?>faculty/section_login/login.php</a>

            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input id="abstractSearchInput" placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div>
            <div class="regi_search_wrap_btn_box">

                <!-- <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a> -->
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newreviewer"><?php add(); ?>New Reviewer</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <div class="filter_body">
                <div>
                    <label>Registration Type</label>
                    <select>
                        <option>All Types</option>
                    </select>
                </div>
                <div>
                    <label>Payment Status</label>
                    <select>
                        <option>All Status</option>
                    </select>
                </div>
                <div>
                    <label>Registration Date</label>
                    <input type="date">
                </div>
            </div>
            <div class="filter_bottom">
                <button><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
        </div>

        <div class="spot_listing">
            <?php
                $counter                   = 0;
                $searchCondition           = " AND grandTAB.id != '1'";
                
                if($_REQUEST['src_faculty_username']!="")
                {
                    $searchCondition      .= " AND grandTAB.faculty_login_username LIKE '%".$_REQUEST['src_faculty_username']."%'";
                }
                if($_REQUEST['src_faculty_name']!="")
                {
                    $searchCondition      .= " AND grandTAB.faculty_full_name LIKE '%".$_REQUEST['src_faculty_name']."%'";
                }
                if($_REQUEST['src_faculty_access_role']!="")
                {
                    $searchCondition      .= " AND grandTAB.faculty_access_role_id = '".$_REQUEST['src_faculty_access_role']."'";
                }
                
                $sqlFacultyDetails['QUERY'] =  "SELECT faculty.*,
                                                        
                                                        IFNULL(faculty.faculty_title, '') AS facultyTitle,
                                                        IFNULL(faculty.faculty_first_name, '') AS facultyFirstName,
                                                        IFNULL(faculty.faculty_middle_name, '') AS facultyMiddleName,
                                                        IFNULL(faculty.faculty_last_name, '') AS facultyLastName,
                                                        faculty.id,
                                                        
                                                        IFNULL(accessRole.faculty_role,'Reviewer') AS faculty_role
                                                    
                                                    FROM "._DB_FACULTY_ACCOUNT_." faculty 
                                                    
                                        LEFT OUTER JOIN "._DB_FACULTY_ACCESS_ROLE_." accessRole 
                                                    ON accessRole.id = faculty.faculty_access_role_id 
                                                
                                                WHERE faculty.status != 'D'";
                                            
                                            
                        
                $resultFacultyDetails      = $mycms->sql_select($sqlFacultyDetails);	
                //echo '<pre>'; print_r($resultFacultyDetails);
                if($resultFacultyDetails)
                {
                    foreach($resultFacultyDetails as $i=>$rowFacultyDetails) 
                    {
                        $counter++;
                        
                        $handCss = '';
                        $allotedAbstracts = getReviewUserAllotmentDetailsArray($rowFacultyDetails['id']);

                        

                        $sqlFacultyAccessRole['QUERY']      = "SELECT COUNT(*) AS COUNTDATA FROM "._DB_ABSTRACT_REQUEST_." A INNER JOIN  "._DB_ABSTRACT_ALLOTMENT_." AL
                                                                ON A.id = AL.abstract_id
                                                                    WHERE AL.`review_user_id` = '".$rowFacultyDetails['id']."' AND A.status='A' ";
                                                                    
                            $resultFacultyAccessRole   = $mycms->sql_select($sqlFacultyAccessRole);


                        $sqlReview['QUERY']      = "SELECT COUNT(*) AS COUNTDATA FROM "._DB_ABSTRACT_REQUEST_." A 
                                                    INNER JOIN "._DB_ABSTRACT_REVIEW_RESULT_." R 
                                                    ON A.id=R.abstract_id

                                                    
                                                                    WHERE R.`faculty_id` = '".$rowFacultyDetails['id']."' AND A.status='A' AND R.status='A' ";
                                                                    
                        $resultReview   = $mycms->sql_select($sqlReview);	

                        //echo '<pre>'; print_r($resultFacultyAccessRole[0]['COUNTDATA']);	

                        // echo '<pre>'; print_r($allotedAbstracts);

                        

                        if(empty($allotedAbstracts['ABSTRACTS']))
                        {
                            $handCss = 'color:#FF0000;';
                        }
                        else
                        {
                            $handCss = 'color:#009900;';
                        }
                        
                        if($allotedAbstracts['HAS_A_UNABLE']=='YES')
                        {
                            $rowStyleDecission = " style='background-color: #FF28FF;'";
                        }									
                        else
                        {
                            $rowStyleDecission = " style='background-color: #FFFFFF;'";
                        }
                ?>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start flex-column">
                        <div class="regi_name"><?= $rowFacultyDetails['faculty_title']." ".$rowFacultyDetails['faculty_first_name']." ".$rowFacultyDetails['faculty_middle_name']." ".$rowFacultyDetails['faculty_last_name']." "?></div>
                        <div class="regi_contact">
                            <span>
                                User Name: <?php
											echo $rowFacultyDetails['faculty_login_username']; 
										?>
                            </span>
                            <span>
                                Password: 
                                <span style="display:none" use="thePassword">
                                    <?=$mycms->decoded($rowFacultyDetails['faculty_login_password'])?>
                                </span>
                                <span use="theDummy">********</span>
                                <a href="javascript:void(0);" style="color:#0066FF;" onclick="showPassword(this);">[view]</a>
                            </span>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Allotment</h5>
                            <h6><?=sizeof($allotedAbstracts['ABSTRACTS'])?></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Access Role</h5>
                            <h6><?=$rowFacultyDetails['faculty_role']?></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Total Review / Total Allocation</h5>
                            <h6><?php echo $resultReview[0]['COUNTDATA']."/".$resultFacultyAccessRole[0]['COUNTDATA']; ?></h6>
                        </div>
                        <div class="accm_details_box action" style="flex:unset;">
                            <h5 class="text-right">Status</h5>
                            <div class="action_div">
                                 <?php	
                                if($rowFacultyDetails['status']=='A'){
                                    ?>
                                    <a href="manage_faculty.process.php?act=<?=($rowFacultyDetails['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFacultyDetails['id']?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                <?php	
                                }else{
                                ?>
                                    <a href="manage_faculty.process.php?act=<?=($rowFacultyDetails['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFacultyDetails['id']?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                <?php	
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom accm_bottom justify-content-end">

                    <div class="spot_box_bottom_right">
                        <a href="#" data-tab="editreviewer"  data-id="<?=$rowFacultyDetails['id']?>"   class="editbtnReview popup-btn badge_secondary icon_hover action-transparent"><?php edit(); ?>Edit</a>
                        <a href="manage_faculty.process.php?act=Remove&id=<?=$rowFacultyDetails['id']?>" class="badge_danger icon_hover action-transparent"  onclick="return confirm('Do you really want to remove this record ?');" ><?php delete(); ?>Delete</a>
                        <a href="allocate_reviewer.php?reviewerId=<?= $rowFacultyDetails['id'] ?>" target="_blank" class="badge_info icon_hover action-transparent"><i class='fal fa-paper-plane'></i>Allocate</a>
                        <a href="#" class="drp icon_hover badge_dark action-transparent">Allotted<?php down() ?></a>
                    </div>
                </div>
                <div class=" accm_tariff spot_service_break">
                    <div class="service_breakdown_wrap mt-0">
                        <h4><?php abstracts(); ?>Allotted Abstracts</h4>
                        <ul class="service_breakdown_wrap_ul">
                            <?
                            	$abstractCounter               = 0;

                                $loggedUserId	= $mycms->getLoggedUserId();
                                $reviewerId 	= trim($rowFacultyDetails['id']);
                                
                                $searchCondition           = " AND grandTAB.id != '1'";
                                
                                $sqlFacultyDetails['QUERY']  = "SELECT faculty.*,	   
                                                                    IFNULL(faculty.faculty_title, '') AS facultyTitle,
                                                                    IFNULL(faculty.faculty_first_name, '') AS facultyFirstName,
                                                                    IFNULL(faculty.faculty_middle_name, '') AS facultyMiddleName,
                                                                    IFNULL(faculty.faculty_last_name, '') AS facultyLastName,
                                                                    IFNULL(accessRole.faculty_role,'Reviewer')  faculty_role
                                                                    
                                                                FROM "._DB_FACULTY_ACCOUNT_." faculty 
                                                                
                                                    LEFT OUTER JOIN "._DB_FACULTY_ACCESS_ROLE_." accessRole 
                                                                ON accessRole.id = faculty.faculty_access_role_id 
                                                                
                                                                WHERE faculty.status != 'D' 
                                                                AND faculty.id = '".$reviewerId."'";
                                $resultFacultyDetails     	 = $mycms->sql_select($sqlFacultyDetails);	
                                $rowFacultyDetails           = $resultFacultyDetails[0];
                                
                                $userCurrAllotmentsIds		 = getReviewUserAllotedAbstractIds($reviewerId);
                                if($userCurrAllotmentsIds)
						    	{
                               
								foreach($userCurrAllotmentsIds as $i=>$abstractId) 
								{
											
									
									
									$abstractDetailsArray = getAbstractDetailsArray($abstractId);
									
									if($abstractDetailsArray['MARKS']['REVIEWER'][$reviewerId]['REVIEW_STATE']=='UNABLE')
									{
										$rowStyleDecission = " style='background-color: #FF28FF;'";
									}
									elseif($abstractDetailsArray['MARKS']['REVIEWER'][$reviewerId]['REVIEW_STATE']=='ABLE')
									{
										$rowStyleDecission = " style='background-color: #DDF1D6;'";
									}
									else
									{
										$rowStyleDecission = " style='background-color: #FFFFFF;'";
									}
									
									
									$abstractColor['ABSTRACT'] 		= "red";
									$abstractColor['CASEREPORT'] 	= "blue";
									
									$presentationColor['ORAL'] 		= "red";
									$presentationColor['POSTER'] 	= "blue";
									$presentationColor['VIDEO'] 	= "orange";		
									
									$coFaculty = array();
									
									foreach($abstractDetailsArray['MARKS']['REVIEWER']	as $id=>$detail)
									{
										if($id!=$reviewerId)
										{
											$coFaculty[] = $detail['NAME'];
										}
									}
									
									if(!empty($abstractDetailsArray))
									{					
							?>
                            <li>
                                <div class="spot_box_top w-100 p-0">
                                    <div class="spot_name d-flex align-items-start">
                                        <div class="regi_img_circle">
                                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                                            <span><?=$abstractCounter?></span>
                                        </div>
                                        <div>
                                            <div class="regi_name"><?=$abstractDetailsArray['RAW']['user_first_name']." ".$abstractDetailsArray['RAW']['user_middle_name']." ".$abstractDetailsArray['RAW']['user_last_name']." "?></div>
                                            <div class="regi_type">
                                                <?php
                                                if($abstractDetailsArray['RAW']['registration_payment_status']=="PAID"  
                                                || $abstractDetailsArray['RAW']['registration_payment_status']=="COMPLIMENTARY"
                                                || $abstractDetailsArray['RAW']['registration_payment_status']=="ZERO_VALUE")
                                                {
                                                    $sqlClassification    = array();
                                                    $sqlClassification['QUERY']    = "SELECT * 
                                                                                                FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " 
                                                                                                WHERE `status` = ? AND `type` = ? AND `id` = ?";

                                                    $sqlClassification['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
                                                    $sqlClassification['PARAM'][]   = array('FILD' => 'type',  'DATA' => 'DELEGATE',  'TYP' => 's');
                                                    $sqlClassification['PARAM'][]   = array('FILD' => 'id',  'DATA' => $abstractDetailsArray['RAW']['registration_classification_id'],  'TYP' => 's');

                                                    $resultClassificationTitle = $mycms->sql_select($sqlClassification);
                                                    ?>
                                                  <span class="badge_padding badge_primary"><?=  $resultClassificationTitle[0]['classification_title'] ?></span>
                                                  <?php if ($abstractDetailsArray['RAW']['registration_tariff_cutoff_id']) { ?>
                                                    <span class="badge_padding badge_primary" style="color: #8ee0f5;!important"><?=getCutoffName($abstractDetailsArray['RAW']['registration_tariff_cutoff_id']) ?></span>

                                                 
                                                <?php } ?>
                                                <?
                                                }												
                                                else //if($rowAbstractDetails['registration_request']=='ABSTRACT')
                                                {
                                                    echo '<span class="badge_padding badge_primary" style="color:#FF0000;">NOT REGISTERED YET</span>';
                                                    
                                                }
                                                
                                               
                                                ?>
                                            </div>
                                            <div class="regi_contact">
                                                <span>
                                                    <i class="fal fa-phone-alt"></i><?=$abstractDetailsArray['RAW']['user_mobile_no']?>
                                                </span>
                                                <span>
                                                    <i class="fal fa-envelope"></i><?=$abstractDetailsArray['RAW']['user_email_id']?>
                                                </span>
                                                <?
                                                 if($abstractDetailsArray['RAW']['registration_payment_status']=="PAID" 
                                                || $abstractDetailsArray['RAW']['registration_payment_status']=="COMPLIMENTARY"
                                                || $abstractDetailsArray['RAW']['registration_payment_status']=="ZERO_VALUE")
                                                {
                                                    ?>
                                                   <span>
                                                    <?php qr() ?><?=strtoupper($abstractDetailsArray['RAW']['user_unique_sequence'])?>
                                                </span>
                                                <?php } ?>
                                                
                                                 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="spot_details">
                                        <div class="spot_details_box">
                                            <h5>Submission Code</h5>
                                            <h6><i class="fal fa-qrcode"></i><b><?=$abstractDetailsArray['SUBMISSION_CODE']?></b></h6>
                                        </div>
                                        <div class="spot_details_box">
                                            <h5>Category</h5>
                                            <h6><?=getCategoryName($abstractDetailsArray['PARENT_TYPE'])?></h6>
                                        </div>
                                        <div class="spot_details_box">
                                            <h5>Topic</h5>
                                            <p><?=$abstractDetailsArray['TOPIC']?></p>
                                            <small><?=$abstractDetailsArray['CONTENT']['TITLE']?></small>
                                        </div>
                                        <div class="spot_details_box">
                                            <h5>Date</h5>
                                            <h6><i class="fal fa-calendar"></i><?= date('d/m/Y h:i A', strtotime($abstractDetailsArray['SUBMISSION_DATE'])) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <? } $abstractCounter++;} } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <? } } ?>

        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
       ///////////////////Reviewer Edit///////////////////////////
   $(document).on('click', '.editbtnReview', function () {

    let reviewerId = $(this).data('id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { reviewerId: reviewerId },
        success: function (response) {

            $('#editreviewer').html($(response).find('#editreviewer').html());

            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditreviewer();
           

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
       ///////////////////Reviewer end///////////////////////////
function showPassword(obj)
{
    var parent = $(obj).closest("span");

    $(parent).find("span[use=theDummy]").toggle();
    $(parent).find("span[use=thePassword]").toggle();
    $(obj).toggle();

    setTimeout(function(){
        $(parent).find("span[use=theDummy]").toggle();
        $(parent).find("span[use=thePassword]").toggle();
        $(obj).toggle();
    }, 10000);
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('abstractSearchInput');
    const abstracts = document.querySelectorAll('.spot_box');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();

        abstracts.forEach(box => {
            const text = box.innerText.toLowerCase();
            if (text.includes(query)) {
                box.style.display = ''; // show
            } else {
                box.style.display = 'none'; // hide
            }
        });
    });
});
</script>
</html>
<? 
function getCategoryName($id)
{
	global $cfg, $mycms;

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `category` 
												   FROM "._DB_ABSTRACT_TOPIC_CATEGORY_."
												 WHERE `id` = '".$id."' AND status='A'";
												 
	
									 
	$resultSelectUser          = $mycms->sql_select($sqlSelectUser);

	//echo '<pre>'; print_r($resultSelectUser);
	return $resultSelectUser[0]['category'];
}
?>
