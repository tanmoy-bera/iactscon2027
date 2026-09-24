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
                      WHERE `status`='A' order by id ";
           //$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$resultsParticipant = $mycms->sql_select($sqlParticipantDate);

//echo '<pre>'; print_r($resultsParticipant[0]['conf_date']); die;
?>

<!DOCTYPE html>
<html>

<?php 
	$loginDetails 	 = login_session_control();
    setTemplateStyleSheet();
    setTemplateBasicJS();
    backButtonOffJS();
 
    include_once('header.php'); 



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

    <section class="main-section programme-wrap">
      <div class="container">
        <div class="row">
          <div class="inner-greadient-sec col-md-12">
            <div class="row">

              <div class="col-lg-12 category-head programme-head">
                <div class="category-left programme-head-left">
                  <h1>Programme Schedule</h1>
                  <p> <?=date('F - Y',strtotime($resultsParticipant[0]['conf_date']));?></p>
                </div>
                <div class="category-right programme-head-right">
                  <a href="<?=_BASE_URL_?>profile.php" class="btn">Back to Previous Page</a>
                </div>
              </div>

              <div class="col-lg-12 pro-list-title d-flex align-items-center">
                <div class="col-md-2 pro-list-title-each pro-date"><img src="images/calender2.png"
                    alt="" />
                </div>
                <div class="col-md-3 pro-list-title-each text-center"><img src="images/cat-ic-2.png"
                    alt="" />
                </div>
                <div
                  class="col-md-7 pro-list-title-each text-center d-flex align-items-center justify-content-center">
                  <img src="images/cat-ic-8.png" alt="" />
                  <h2>Highlights</h2>
                </div>
              </div>

              <div class="pro-list-row-wrap col-12">

	              	<?php 
	              	foreach($resultsParticipant as $k=>$val)
	              	{
	              	?>
		                <div class="row pro-list-each-row">
		                  <a href="programme-details.php?id=<?=$val['id']?>" class="d-flex align-items-center">
		                    <div class="col-md-2 pro-list-info-each pro-date">
		                      <h2><?=date('d',strtotime($val['conf_date']))?></h2>
		                      <p><?=date('F',strtotime($val['conf_date']))?></p>
		                      <p><?=date('H:i',strtotime($val['conf_date']))?></p>
		                    </div>
		                    <div class="col-md-3 pro-list-info-each text-center">
		                      <h2><?=$val['conf_title']?></h2>
		                      <h3><?=$val['conf_desc']?></h3>
		                    </div>
		                    <div class="col-md-7 pro-list-info-each pro-highlight d-flex">
		                    	<?php
		                    	if(!empty($val['conf_image']))
		                    	{
		                    	?>
		                    	  <div class="pro-highlight-img"><img src="<?=_BASE_URL_.$cfg['SP.PARTICIPANT.DOC'].$val['conf_image']?>"
		                          alt="" /> </div>
		                        <?php
		                        }
		                        ?>  

		                      <div class="pro-highlight-info">
		                        <?=$val['conf_highlight']?>
		                      </div>
		                    </div>
		                  </a>
		                </div>
		            <?php
		            }	
		            ?>    

	               
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>

   

  </main>

</body>

</html>
