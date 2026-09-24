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
<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Reviewer</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
     <?
     $loggedUserId	= $mycms->getLoggedUserId();
	 $reviewerId 	= trim($_REQUEST['reviewerId']);
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
        // echo "<pre>";
        // print_r($userCurrAllotmentsIds);
    ?>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Reviewer</a></li>
                        <li class="breadcrumb-item"><a href="#">Reviewer Listing</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Allocation</li>
                    </ol>
                </nav>
                <h2>Abstract / Case Report Allotment</h2>
                <h6>Issue ID cards, kits, and manage on-spot requests.</h6>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input id="searchInput" placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
             <form name="frmSearch" id="frmSearch"  method="post">
            <div class="filter_body">
                <div>
                    <label>Presentation Type:</label>
                     <select name="src_presentation_type" id="src_presentation_type">
                        <option value="" >All Types</option>
                        <option  value="ORAL" >ORAL</option>
                        <option  value="POSTER" >POSTER</option>
                        <option  value="VIDEO" >VIDEO</option>
                    </select>
                </div>
                <div>
                    <label>Nomination</label>
                     <select name="src_abstract_award_id" id="src_abstract_award_id" >
                        <option value="">-- Select Nomination --</option>
                        <?php
                        $sqlAbstractAward    = array();
                        $sqlAbstractAward['QUERY']    = "SELECT * 
                                                            FROM "._DB_AWARD_MASTER_." 
                                                            WHERE `status` = ? 
                                                        ORDER BY `award_name` ASC";
                                                        
                        $sqlAbstractAward['PARAM'][]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');
                        
                        $resultAbstractAward = $mycms->sql_select($sqlAbstractAward);
                        if($resultAbstractAward)
                        {
                            foreach($resultAbstractAward as $keyAbstractAward=>$rowAbstractAward)
                            {
                        ?>
                                <option value="<?=$rowAbstractAward['id']?>" <?=($rowAbstractAward['id']==$_REQUEST['src_abstract_award_id'])?'selected="selected"':''?>><?=$rowAbstractAward['award_name']?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
               
                <div>
                    <label>Type:</label>
                    <select name="src_type" id="src_type">
                        <option value="">All Types</option>
                        <option  value="ABSTRACT" >ABSTRACT</option>
                        <option  value="CASEREPORT" >CASEREPORT</option>
                    </select>
                </div>
            </div>
            <div class="filter_bottom">
                <button onclick="clearFilters();"><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
          </form>
        </div>
        <div class="workshop_overview_wrap mb-3">
            <h2 class="sub_head">Reviewer Details</h2>
            <ul>
                <li>Name<span><?= $rowFacultyDetails['faculty_title']." ".$rowFacultyDetails['faculty_first_name']." ".$rowFacultyDetails['faculty_middle_name']." ".$rowFacultyDetails['faculty_last_name']." "?></span></li>
                <li>User Name<span><?=$rowFacultyDetails['faculty_login_username']?></span></li>
                <li>Allotment<span><?=sizeof($userCurrAllotmentsIds)?></span></li>
                <li>Access Role<span>1</span></li>
                <li>Total Review / Total Allocation<span>0</span></li>
            </ul>
        </div>
       
        <div class="spot_listing" id="abstractSearchInput">
        <?php					
                $searchCondition               = "";
                $abstractCounter               = 0;
                if($_REQUEST['src_paper_presentation_category']!="")
                {
                    $searchCondition          .= " AND abstractRequest.abstract_child_type = '".$_REQUEST['src_paper_presentation_category']."'";
                }
                if($_REQUEST['src_abstract_topic_id']!="")
                {
                    $searchCondition          .= " AND abstractRequest.abstract_topic_id = '".$_REQUEST['src_abstract_topic_id']."'";
                }
                if($_REQUEST['src_presentation_type']!="")
                {
                    $searchCondition          .= " AND abstractRequest.abstract_child_type = '".$_REQUEST['src_presentation_type']."'";
                }
                if($_REQUEST['src_abstract_award_id']!="")
                {
                    $searchCondition          .= " AND abstractRequest.id IN ( SELECT submission_id FROM "._DB_AWARD_REQUEST_." WHERE award_id = '".$_REQUEST['src_abstract_award_id']."' AND status = 'A' )";
                }
                if($_REQUEST['src_type']!="")
                {
                    $searchCondition          .= " AND abstractRequest.abstract_parent_type = '".$_REQUEST['src_type']."'";
                }
                
                $abstractCounter               = 0;
                $sqlAbstractDetails			   = abstractDetailsQuerySet("",$searchCondition);
                
                $resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);	
            if($resultAbstractDetails)
            {
                foreach($resultAbstractDetails as $i=>$rowAbstractDetails) 
                {
                    $totalAbstractCount				= getTotalAbstractCount($rowAbstractDetails['applicant_id']);
                    $totalCaseCount 				= getTotalCaseCount($rowAbstractDetails['applicant_id']);

                    $abstractCounter++;
                    
                    $ReviewCount  = array();
                    $ReviewCount['QUERY'] 		= " SELECT COUNT(*) AS totalReviewAttemptCount 
                                                        FROM "._DB_ABSTRACT_REVIEW_RESULT_."
                                                        WHERE `abstract_id` = ?"; 
                                                        
                    $ReviewCount['PARAM'][]    = array('FILD' => 'abstract_id',  'DATA' =>$rowAbstractDetails['id'],  'TYP' => 's');
                    
                    $resultReviewCount  = $mycms->sql_select($ReviewCount);
                    $rowReviewCount		= $resultReviewCount[0];
                    
                    $ReviewMarks       = array();			
                    $ReviewMarks['QUERY'] 		= "SELECT SUM(marks_obtained) AS totalMarksObtained 
                                                        FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
                                                    WHERE `abstract_id` = ? 
                                                        AND `status` =  ?"; 
                                                        
                    $ReviewMarks['PARAM'][]    = array('FILD' => 'abstract_id',  'DATA' =>$rowAbstractDetails['id'],  'TYP' => 's');
                    $ReviewMarks['PARAM'][]    = array('FILD' => 'status',       'DATA' =>'A',                        'TYP' => 's');
                    
                    $resultReviewMarks  = $mycms->sql_select($ReviewMarks);
                    $rowReviewMarks     = $resultReviewMarks[0];
                                            
                    $rowStyleDecission     = "";
                    if($rowReviewCount['totalReviewAttemptCount']==0)
                    {
                        $rowStyleDecission = " style='background-color: #FFFFFF;'";
                    }
                    else
                    {
                        $rowStyleDecission = " style='background-color: #DDF1D6;'";
                    }
                    $sqlClassification    = array();
                    $sqlClassification['QUERY']    = "SELECT * 
                                                                FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " 
                                                                WHERE `status` = ? AND `type` = ? AND `id` = ?";

                    $sqlClassification['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
                    $sqlClassification['PARAM'][]   = array('FILD' => 'type',  'DATA' => 'DELEGATE',  'TYP' => 's');
                    $sqlClassification['PARAM'][]   = array('FILD' => 'id',  'DATA' => $rowAbstractDetails['registration_classification_id'],  'TYP' => 's');

                    $resultClassificationTitle = $mycms->sql_select($sqlClassification);

                    $abstractColor['ABSTRACT'] 		= "red";
                    $abstractColor['CASEREPORT'] 	= "blue";
                    
                    $presentationColor['ORAL'] 		= "red";
                    $presentationColor['POSTER'] 	= "blue";
                    $presentationColor['VIDEO'] 	= "orange";
                    
                    $abstractCurrentAllotedUsers   = getAbstractAllotedUserIds($rowAbstractDetails['id']);
                    $abstractCurrentAllotedUsers[] = 0;
                    
                    $currentAllotedUsers		= array();
                    $sqlFacultyDetails			= array();
                    $sqlFacultyDetails['QUERY'] =  "SELECT faculty.*,
                                                            
                                                            IFNULL(faculty.faculty_title, '') AS facultyTitle,
                                                            IFNULL(faculty.faculty_first_name, '') AS facultyFirstName,
                                                            IFNULL(faculty.faculty_middle_name, '') AS facultyMiddleName,
                                                            IFNULL(faculty.faculty_last_name, '') AS facultyLastName,
                                                            faculty.id
                                                        
                                                        FROM "._DB_FACULTY_ACCOUNT_." faculty 
                                                        
                                                        WHERE faculty.id IN (".implode(',',$abstractCurrentAllotedUsers).")";
                            
                    $resultFacultyDetails      = $mycms->sql_select($sqlFacultyDetails);	
                    if($resultFacultyDetails)
                    {
                        foreach($resultFacultyDetails as $i=>$rowFacultyDetails) 
                        {
                            $currentAllotedUsers[] = $rowFacultyDetails['faculty_title']." ".$rowFacultyDetails['faculty_first_name']." ".$rowFacultyDetails['faculty_middle_name']." ".$rowFacultyDetails['faculty_last_name'];
                        }
                    }
                    
            ?>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span><?=$abstractCounter?></span>
                        </div>
                        <div>
                            <div class="regi_name"><?=$rowAbstractDetails['user_first_name']." ".$rowAbstractDetails['user_middle_name']." ".$rowAbstractDetails['user_last_name']." "?></div>
                             <?php
									if (
										$rowAbstractDetails['registration_payment_status'] == "PAID"
										|| $rowAbstractDetails['registration_payment_status'] == "COMPLIMENTARY"
										|| $rowAbstractDetails['registration_payment_status'] == "ZERO_VALUE"
									) {
                                        ?>
                            <div class="regi_type">
                               
                                <span class="badge_padding badge_primary"><?= $resultClassificationTitle[0]['classification_title'] ?></span>
                                <?php if (getCutoffName($rowAbstractDetails['registration_tariff_cutoff_id'])) { ?>
                                    <span class="badge_padding badge_secondary">
                                        <?php
                                        echo '<span style="color: #8ee0f5; text-transform: uppercase;" title="Cutoff">' . getCutoffName($rowAbstractDetails['registration_tariff_cutoff_id']) . '</span>';

                                        ?></span>
                                <?php } ?>
                            </div>
                            <? } else{
                                ?>
                                  <div class="regi_type">
                                     <span class="badge_padding badge_primary" style="color: #FF0000;!important" >Not Registered Yet</span>
                                 </div>
                                <?
                            }
                                ?>
                            <div class="regi_contact">
                                <span>
                                    <i class="fal fa-phone-alt"></i><?=$rowAbstractDetails['user_mobile_no']?>
                                </span>
                                <span>
                                    <i class="fal fa-envelope"></i><?=$rowAbstractDetails['user_email_id']?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Submission Code</h5>
                            <h6><i class="fal fa-qrcode"></i><b><?=$rowAbstractDetails['abstract_submition_code']?></b></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Category</h5>
                            <h6><?= getCategoryName($rowAbstractDetails['abstract_cat']) ?></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Topic</h5>
                            <p><?=$rowAbstractDetails['abstract_topic']?></p>
                            <small><?=$rowAbstractDetails['abstract_title']?></small>
                        </div>
                        <div class="spot_details_box">
                            <h5>Date</h5>
                            <h6><i class="fal fa-calendar"></i><?= date('d/m/Y h:i A', strtotime($rowAbstractDetails['created_dateTime'])) ?></h6>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom justify-content-end">

                    <div class="spot_box_bottom_right">
                        <a href="#" class=" badge_dark icon_hover action-transparent">Allot <label class="toggleswitch">
                                <input class="toggleswitch-checkbox"  use="checkAbstract" type="checkbox" name="abstractId[]" value="<?=$rowAbstractDetails['id']?>" abstractId="<?=$rowAbstractDetails['id']?>" reviewerId="<?=$reviewerId?>" <?=in_array($rowAbstractDetails['id'],$userCurrAllotmentsIds)?"checked":""?>>
                                <div class="toggleswitch-switch"></div>
                            </label></a>
                    </div>
                </div>
            </div>
           <? } 
           $abstractCounter++ ;
           } ?>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); 
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
}?>

</html>
 <script>
    function clearFilters() {
    // Get the form
        var form = document.forms['frmSearch'];

        // Clear selects
        form.src_presentation_type.value = "";
        form.src_abstract_award_id.value = "";
        form.src_abstract_topic_id.value = "";
        form.src_type.value = "";

        // Clear date input
        form.querySelector('input[type="date"]').value = "";

       
        form.submit();
    }
    function selectAllAbstracts(obj)
    {
        if($(obj).is(":checked"))
        {
            $("input[type=checkbox][use=checkAbstract]").prop("checked",true);
        }
        else
        {
            $("input[type=checkbox][use=checkAbstract]").prop("checked",false);
        }
    }
    $(document).ready(function(){
        $("input[type=checkbox][use=checkAbstract]").click(function(){
                                          
            var checkBox = $(this);
            var parent = $(checkBox).parent().closest("tr");
            $(checkBox).hide();
            $(parent).find("img[use=checkAbstractProcessLoader]").show();
            <?
            if($loggedUserId!='1')
            {
            ?>
                alert("upgradation ongoing. Please wait.");
                $(checkBox).show();
                $(parent).find("img[use=checkAbstractProcessLoader]").hide();
            <?
            }
            else
            {
            ?>
            var abstractId = $(checkBox).attr('abstractId');
            var reviewerId = $(checkBox).attr('reviewerId');
            var whatToDo = 'DELETE';
            if($(checkBox).is(":checked"))
            {
                whatToDo = 'INSERT';
            }
            
            var dataValue = "act=allocateIndividualAbstract&abstractId="+abstractId+"&reviewerId="+reviewerId+"&whatToDo="+whatToDo+"";
             
            $.ajax({
                    type : 'POST',
                    data: dataValue,
                    url : "manage_faculty.process.php",
                    success: function(data){
                                // alert("Data Updated successfully!.");
                                }
                }).fail(function() {
                    alert("Something Wrong!! Could not update.");
                    window.location.reload();
                }).always(function() {
                    $(parent).find("img[use=checkAbstractProcessLoader]").hide();
                    $(checkBox).show();
                });		
            
            <?
            }
            ?>
        });
    });
    $(document).ready(function() {
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $("#abstractSearchInput .spot_box").filter(function() {
                // Check if any text inside this spot_box matches
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>