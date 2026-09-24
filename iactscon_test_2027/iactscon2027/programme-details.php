<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.abstract.php");
include_once('includes/function.accommodation.php');

$sql    =   array();
$sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
                      WHERE `status`='A' order by id desc limit 1";
                     //$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
$result = $mycms->sql_select($sql);
$row             = $result[0];

$header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['header_image'];
if($row['header_image']!='')
{
    $emailHeader  = $header_image;
}

$sqlIcon  = array();
$sqlIcon['QUERY'] = "SELECT * FROM "._DB_ICON_SETTING_." 
                      WHERE `status`='A' order by id ";
           //$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$resultIcon = $mycms->sql_select($sqlIcon);

$title = 'Program'; 


$sqlParticipantDate  = array();
$sqlParticipantDate['QUERY'] = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_DATE_." 
                      WHERE `status`='A' order by conf_date ";
           //$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$resultsParticipantDate = $mycms->sql_select($sqlParticipantDate);

//echo '<pre>'; print_r($resultsParticipantDate); die;

  $sqlParti = array();
  $sqlParti['QUERY']     = " SELECT *
          FROM "._DB_PROGRAM_HIGHLIGHT_SPEAKER_." participant_details
           
     
           WHERE status = 'A' ";
                                   
    $resParti   = $mycms->sql_select($sqlParti);

  //echo '<pre>'; print_r($resParti); die;


?>
<!DOCTYPE html>
<html>

<?php 
    $loginDetails    = login_session_control();
    $delegateId    = $loginDetails['DELEGATE_ID'];
    $rowUserDetails  = getUserDetails($delegateId);
    setTemplateStyleSheet();
    setTemplateBasicJS();
    backButtonOffJS();
 
    include_once('header.php'); 

   // echo '<pre'; print_r($rowUserDetails); die;

  $sqlParticipantHall  = array();
  $sqlParticipantHall['QUERY'] = "SELECT * FROM "._DB_MASTER_HALL_." 
                        WHERE `status`='A' order by id ";
                   
  $resultsParticipantHall = $mycms->sql_select($sqlParticipantHall);  

 ?>

<body class="single inner-page">

  <main>

    <div class="media-icons" data-aos="flip-left" data-aos-duration="800">
      <div class="media-bottom">
        <a href="#" class="media-hidden"><img src="images/ic-med5.png" alt="" /></a>
        <a href="#" class="media-hidden"><img src="images/ic-med3.png" alt="" /></a>
        <a href="#" class="media-hidden"><img src="images/ic-med.png" alt="" /></a>
        <a href="#" class="media-hidden"><img src="images/ic-med2.png" alt="" /></a>
        <a href="#" class="media-hidden"><img src="images/ic-med4.png" alt="" /></a>
        <span class="media-click"><img src="images/ic-all-social.png" alt="" /></span>
      </div>

      <div class="media-label"><span>FOLLOW US</span></div>
    </div>

    <div class="dashbord-tow-menu sidebar1">
      <div class="close"><img src="images/close.svg" alt="" /></div>
      <div class="register-header">
        <img src="images/cat-ic-1.png" alt="">
        <h3>Registration</h3>
      </div>

      <div class="register-header2">
        <ul>
          <li>
            <img src="images/badge1.png" alt="">
            <h5>Member.</h5>
          </li>
          <li>
            <img src="images/badge2.png" alt="">
            <h5>Registration ID</h5>
          </li>
        </ul>

        <a href="#" class="btn">Invoice</a>
      </div>

      <ul class="dashbord-tow-main-mnu">
        <li><a href="#"><img src="images/cat-ic-2.png" alt=""> Workshop</a></li>
        <li><a href="#"><img src="images/cat-ic-3.png" alt=""> Accompaning</a></li>
        <li><a href="#"><img src="images/cat-ic-6.png" alt=""> Accomodation</a></li>
      </ul>
    </div>

    <div class="pro-details-sidebar">
      <div class="pro-details-top">
        <img src="<?=_BASE_URL_?>images/dummy.jpg" alt="" />

        <div class="profile-details">
          <div class="profile-dtils-box">
            <label>Name</label>
            <p><?=$rowUserDetails['user_full_name'];?></p>
          </div>
          <div class="profile-dtils-box">
            <label>Phone</label>
            <p><?=$rowUserDetails['user_mobile_no'];?></p>
          </div>
          <div class="profile-dtils-box">
            <label>Email</label>
            <p><?=$rowUserDetails['user_email_id'];?></p>
          </div>
          <div class="profile-dtils-box">
            <label>Registration ID</label>
            <p><?=$rowUserDetails['user_registration_id'];?></p>
          </div>
        </div>

       <input type="hidden" name="conferenceId" id="conferenceId" value="<?=$_REQUEST['id']?>">
      </div>
      <div class="pro-hall-list text-center ">
        <h4 class="text-decoration-underline ">Select Hall</h4>
        <ul class="add-inclusion">
          <?php
           foreach($resultsParticipantHall as $k=>$val)
           {
          ?>
             <li><a href="javascript:void(0)" id="getDataByHall" hall_id="<?=$val['id']?>"><?=$val['hall_title']?></a></li>
          <?php
          }
          ?>   
          
        </ul>
      </div>

    </div>

    <section class="main-section programme-wrap">
      <div class="container">
        <div class="row">
          <div class="inner-greadient-sec col-md-12 pro-details-wrap">
            <div class="row">

              <div class="col-lg-12 category-head programme-head">
                <div class="category-left programme-head-left speaker-title">
                  <h3>Our Speakers</h3>
                </div>
                <div class="category-right programme-head-right">
                  <a href="<?=_BASE_URL_?>programme.php" class="btn">Back to Previous Page</a>
                </div>
              </div>

              <div class="col-12 speaker-slider-wrap">
                  <div class="speaker-slider">

                      <?php
                      foreach($resParti as $k=>$val)
                      {
                      ?>
                        <div class="item d-flex">
                            <div class="speaker-img">
                              <?php
                                if($val['image']!='')
                                {
                                ?>
                                  <img src="<?=_BASE_URL_.$cfg['SP.PARTICIPANT.DOC'].$val['image']?>" alt="" />
                                <?php
                                }
                                else
                                {
                                  ?>
                                  <img src="<?=_BASE_URL_.'images/dummy-profile-pic-male1.jpg';?>" alt="" />
                                  <?php
                                }
                                ?>
                            </div>
                             <div class="speaker-info">
                                <h5><?=$val['speaker_name']?></h5>
                                <h6><?=$val['designation']?></h6>
                                <h6><?=date('F jS',strtotime($val['conf_datetime']));?></h6>
                                <h6><?=$val['hall_name']?></h6>
                                <h3><?=date('H:i',strtotime($val['conf_datetime']));?></h3>
                            </div>
                        </div>
                      <?php
                      }
                      ?>  

                      

                  </div>
              </div>

                <div class="pro-tab-wrap">
                  <div class="tab-title">
                    <h3>Programme Schedule</h3>
                  </div>

                  <div id="tabs">
                      <ul class="d-flex p-0">
                        <?php
                          $i=1;
                          $startId ='';
                          foreach($resultsParticipantDate as $k=>$value)
                          { 

                            if($i==1)
                            {
                              $startId =$value['id'];
                            }
                        ?>
                            <li class="list-unstyled" match-id="<?=$value['id']?>"><a id="getTabs" conf_id="<?=$value['id']?>"  href="#tabs-<?=$i?>"><?=date('F d',strtotime($value['conf_date']))?></a></li>
                        <?php
                           $i++;
                         } 
                        ?>  

                      </ul>
                        <?php
                          $i=1;
                          foreach($resultsParticipantDate as $k=>$value)
                          { 
                        ?>
                            <div id="tabs-<?=$i?>" class="tabs-content">
                               
                            </div>
                        <?php
                           $i++;
                          }
                        ?>    

                  </div>
                </div>

            </div>
          </div>
        </div>
      </div>
    </section>

  </main>
<script type="text/javascript">
  $(document).ready(function(){
      $("#tabs").tabs();
      
      $(document).on('click', '#getTabs', function(){
          var conf_id = $(this).attr('conf_id');
          var delegateId ='<?=$delegateId?>';
          if(conf_id!= undefined && conf_id!='')
          {
              $('#conferenceId').val(conf_id);
              $('.tabs-content').html("");
                $.ajax({
                  type: "POST",
                  url: jsBASE_URL + 'login.process.php',
                  data: 'action=getNewTabData&conf_id=' + conf_id+'&delegatetID='+delegateId,
                  dataType: 'json',
                  async: false,
                  success: function(JSONObject) {
                      console.log(JSONObject);

                      
                      if(JSONObject.succ==200)
                      {
                        
                          $('.tabs-content').html(JSONObject.data);
                          toastr.success('Program schedule added', 'Success', {
                              "progressBar": true,
                              "timeOut": 2000, 
                              "showMethod": "slideDown", 
                              "hideMethod": "slideUp"    
                           });
                       
                      }

                  }
              });
          }  
          

      });

      $(document).on('click', '#getDataByHall', function(){
          var hall_id = $(this).attr('hall_id');
          var delegateId ='<?=$delegateId?>';
          if(hall_id!= undefined && conf_id!='')
          {
            $(this).addClass('active');
            var conf_id = $('#conferenceId').val();
             $('.tabs-content').html("");
                $.ajax({
                  type: "POST",
                  url: jsBASE_URL + 'login.process.php',
                  data: 'action=getNewTabData&conf_id=' + conf_id+'&delegatetID='+delegateId+'&hall_id='+hall_id,
                  dataType: 'json',
                  async: false,
                  success: function(JSONObject) {
                      console.log(JSONObject);
                      
                      if(JSONObject.succ==200)
                      {
                        
                          $('.tabs-content').html(JSONObject.data);
                          toastr.success('Program schedule added', 'Success', {
                              "progressBar": true,
                              "timeOut": 2000, 
                              "showMethod": "slideDown", 
                              "hideMethod": "slideUp"    
                           });
                       
                      }

                  }
              });
          }  
          

      });

      var startId = '<?=$_REQUEST['id']?>';
      var delegateId ='<?=$delegateId?>';

       if(startId!= undefined && startId!='')
        {
           $('.tabs-content').html("");
              $.ajax({
                type: "POST",
                url: jsBASE_URL + 'login.process.php',
                data: 'action=getNewTabData&conf_id=' + startId+'&delegatetID='+delegateId,
                dataType: 'json',
                async: false,
                success: function(JSONObject) {
                    console.log(JSONObject);

                    
                    if(JSONObject.succ==200)
                    {
                      
                        $('.tabs-content').html(JSONObject.data);
                        toastr.success('Program schedule added', 'Success', {
                            "progressBar": true,
                            "timeOut": 2000, 
                            "showMethod": "slideDown", 
                            "hideMethod": "slideUp"    
                         });
                     
                    }

                    
                }
            });
        } 

       $('.list-unstyled').each(function() {
            var conf_id = $(this).attr('match-id');
            if(startId!='')
            {
               $(this).removeClass('ui-tabs-active');
            }
           
           if(startId===conf_id)
            {
               $(this).addClass('ui-tabs-active');
            }
        }); 
      
  });
</script>
</body>

</html>