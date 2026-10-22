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
      $(document).on('click', '.editReviewCatbtn', function() {
        
        var id     = $(this).data('id');
        var category  = $(this).data('category');
        var score   = $(this).data('score');
        var status  = $(this).data('status');


        $('#reviewCatId').val(id);
        $('#categoryReview').val(category);
        $('#scoreReview').val(score);
     

        if(status === 'A') {
            $('#edit_cat_active').prop('checked', true);
        } else {
            $('#edit_cat_inactive').prop('checked', true);
        }
        // Trigger the popup-btn functionality
    $('#editreviewcategory').fadeIn(); // or your popup open function
    });
    ////////////review category end////////////////////
  ///////////////////Reviewer Edit///////////////////////////
   $(document).on('click', '.revieweditbtnlist', function () {

    let reviewerScoreId = $(this).data('id');

    $.ajax({
        url: 'includes/popup.php',
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

</html>