<?php
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.workshop.php");
?>
<!DOCTYPE html>
<html lang="en">
<?php
setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();
include_once('header.php');

$loginDetails    = login_session_control();
$delegateId    = $loginDetails['DELEGATE_ID'];

$id = addslashes(trim($_REQUEST['Submissionid']));
$sqlQuery = array();
$sqlQuery['QUERY']     = "  SELECT abstractRequest.*,
										   abstractTopic.abstract_topic AS abstract_topic												  														  
												
									  FROM " . _DB_ABSTRACT_REQUEST_ . " abstractRequest 																			  
									  
						   LEFT OUTER JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic 
										ON abstractRequest.abstract_topic_id = abstractTopic.id 
																																
									 WHERE abstractRequest.abstract_submition_code = ?";
$sqlQuery['PARAM'][]     = array('FILD' => 'abstract_submition_code',    'DATA' => $id,     'TYP' => 's');

$abstractDetails      = $mycms->sql_select($sqlQuery, false);
$rowAbstractDetails    = $abstractDetails[0];

$rowUserDetails     = getUserDetails($rowAbstractDetails['applicant_id']);

$sqlAbstractTopic       = array();
$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                      WHERE `status` ='A' AND id='" . $rowAbstractDetails['abstract_cat'] . "'
                     ORDER BY `category` ASC";


$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

//echo '<pre>'; print_r($resultAbstractTopic[0]['category']);

$abstract_background_aims_words   = explode(' ', trim($rowAbstractDetails['abstract_background_aims']));
$abstract_material_methods_words   = explode(' ', trim($rowAbstractDetails['abstract_material_methods']));
$abstract_results_words       = explode(' ', trim($rowAbstractDetails['abstract_results']));
$abstract_conclution_words       = explode(' ', trim($rowAbstractDetails['abstract_conclution']));
$abstract_references_words       = explode(' ', trim($rowAbstractDetails['abstract_references']));
$abstract_background_words       = explode(' ', trim($rowAbstractDetails['abstract_background']));
$abstract_description_words     = explode(' ', trim($rowAbstractDetails['abstract_description']));

$totalWordCount            = count($abstract_background_aims_words)
  + count($abstract_material_methods_words)
  + count($abstract_results_words)
  + count($abstract_conclution_words)
  + count($abstract_references_words)
  + count($abstract_background_words)
  + count($abstract_description_words);
$total_count_word = 0;
if (trim($rowAbstractDetails['abstract_category']) === "Free Paper" && strtoupper(trim($rowAbstractDetails['abstract_parent_type']) === "CASE REPORT")) {

  $intro_words = str_word_count(trim($rowAbstractDetails['abstract_background']));
  $desc_words = str_word_count(trim($rowAbstractDetails['abstract_description']));
  $conclution_words = str_word_count(trim($rowAbstractDetails['abstract_conclution']));
  $total_count_word = $intro_words + $desc_words + $conclution_words;
} else {

  $intro_words = str_word_count(trim($rowAbstractDetails['abstract_background']));
  $aims_obj_words = str_word_count(trim($rowAbstractDetails['abstract_background_aims']));
  $material_wrods = str_word_count(trim($rowAbstractDetails['abstract_material_methods']));
  $results_wrods = str_word_count(trim($rowAbstractDetails['abstract_results']));
  $conclution_words = str_word_count(trim($rowAbstractDetails['abstract_conclution']));
  $total_count_word = $intro_words + $aims_obj_words + $material_wrods + $results_wrods + $conclution_words;
}
//echo 'total:-'.$total_count_word;
$sql            = array();
$sql['QUERY']           = "   SELECT mast.award_name 
										FROM " . _DB_AWARD_REQUEST_ . " req
								  INNER JOIN " . _DB_AWARD_MASTER_ . " mast
										  ON mast.`id` = req.`award_id`
									   WHERE req.`applicant_id` = '" . $rowAbstractDetails['applicant_id'] . "'
										 AND req.`submission_id` = '" . $rowAbstractDetails['id'] . "'
										 AND req.`status`= 'A'";
$resultUsertAward       = $mycms->sql_select($sql, false);
$nominations         = array();
//echo '<!-- nomination --'; print_r($sql); echo '-->';
foreach ($resultUsertAward as $kk => $row) {
  $nominations[] = $row['award_name'];
}

if (empty($nominations)) {
  $nominations[] = 'None';
}

$sqlSuccessImg    =   array();
$sqlSuccessImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . "
                            WHERE `title`='Abstract Submission Success Image' ";

$resultSuccessImg      = $mycms->sql_select($sqlSuccessImg);
$SuccessImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSuccessImg[0]['image'];

?>

<body class="single inner-page abstarct-submition">
  <div class="body-frm">
    <main>

      <?php
      //fetching social media icon
      $sql     =    array();
      $sql['QUERY']    = "SELECT * FROM " . _DB_SOCIAL_ICON_SETTING_ . " 
                        WHERE `purpose`='Regular Icon' AND `status`='A'";
      $resultSocialIcon            = $mycms->sql_select($sql);

      // $valSocial    	= $result[0];

      $sql                =    array();
      $sql['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_;
      $result            = $mycms->sql_select($sql);
      $social_icon_text    =    $result[0]['social_icon_text'];

      if ($resultSocialIcon) {
      ?>
        <div class="media-icons" data-aos="flip-left" data-aos-duration="800">
          <div class="media-bottom">
            <?php
            foreach ($resultSocialIcon as $k => $valSocial) {
              $icon_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $valSocial['icon'];
            ?>
              <a href="<?= $valSocial['link']; ?>" target="_blank" class="media-hidden"><img src="<?= $icon_image ?>" alt="" /></a>
            <?php
            }

            $sql     =    array();
            $sql['QUERY']    = "SELECT * FROM " . _DB_SOCIAL_ICON_SETTING_ . " 
                            WHERE `purpose`='Button Icon'";
            $result            = $mycms->sql_select($sql);
            $valSocialButton        = $result[0]['icon'];
            ?>
            <span class="media-click"><img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $valSocialButton ?>" width="40px" height="40px" alt="" /></span>
          </div>

          <div class="media-label"><span><?= $social_icon_text ?></span></div>
        </div>
      <?php } ?>

      <div class="dashbord-tow-menu sidebar1">
        <div class="close"><img src="<?= _BASE_URL_ ?>images/close.svg" alt="" /></div>
        <div class="register-header">
          <img src="<?= _BASE_URL_ ?>images/cat-ic-1.png" alt="">
          <h3>Registration</h3>
        </div>

        <div class="register-header2">
          <ul>
            <li>
              <img src="<?= _BASE_URL_ ?>images/badge1.png" alt="">
              <h5>Member.</h5>
            </li>
            <li>
              <img src="<?= _BASE_URL_ ?>images/badge2.png" alt="">
              <h5>Registration ID</h5>
            </li>
          </ul>

          <a href="#" class="btn">Invoice</a>
        </div>

        <ul class="dashbord-tow-main-mnu">
          <li><a href="#"><img src="<?= _BASE_URL_ ?>images/cat-ic-2.png" alt=""> Workshop</a></li>
          <li><a href="#"><img src="<?= _BASE_URL_ ?>images/cat-ic-3.png" alt=""> Accompaning</a></li>
          <li><a href="#"><img src="<?= _BASE_URL_ ?>images/cat-ic-6.png" alt=""> Accomodation</a></li>
        </ul>
      </div>

      <section class="main-section">
        <div class="container">
          <div class="note-section">
            <div class="row">

              <div class="col-lg-6">
                <div class="note-icon">
                  <div class="note-icon-mx">
                    <img src="<?= $SuccessImg ?>" alt="" />
                    <h2><?= $cfg['ABSTRACT.SUCCESS.IMAGE.TEXT'] ?></h2>
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="note-details">
                  <!-- <h1>Noted!</h1>

                <p>Dear <?= $rowUserDetails['user_full_name'] ?></p>

                <p>We've recorded your Abstract Submission.</p>

                <div class="group-text">
                  <p>Submission Reference No : <span><?= $rowAbstractDetails['id'] ?></span></p>
                  <p><text>Submission Category: <?= ucwords($resultAbstractTopic[0]['category']); ?></text></p>
                  <p><text>Title : <?= $rowAbstractDetails['abstract_title'] ?></text></p>
                </div>

                <div class="group-text">
                  <p>Registration is mandatory for all oral/poster
                    presenters by <span>August 12, 2023.</span></p>
                  <p>The Presenter must be one of the Authors.</p>
                </div>

                <p>Please note :</p>
                <ul>
                  <li>Resubmission of the same abstract must be avoided.</li>
                  <li>Last date of submission <?= $mycms->cDate('d/m/Y', $cfg['ABSTRACT.CONFIRMATION.DATE']) ?>.</li>
                </ul>

                <div class="group-text">
                  <p>Your Submission Code may be needed for
                    further references.</p>
                  <p>If needed, you may write at <span><?= $cfg['EMAIL_CONF_EMAIL_US'] ?></span></p>
                </div> -->
                  <?php
                  $find = ['[USER]', '[REF_NO]', '[CATEGORY]', '[ABSTRACT_TITLE]', '[ABSTRACT_CONFIRMATION_DATE]', '[CONF_EMAIL]'];
                  $replacement = [
                    $rowUserDetails['user_full_name'],
                    $rowAbstractDetails['id'],
                    ucwords($resultAbstractTopic[0]['category']),
                    ucwords($rowAbstractDetails['abstract_title']),
                    $mycms->cDate('d/m/Y', $cfg['ABSTRACT.CONFIRMATION.DATE']),
                    $cfg['EMAIL_CONF_EMAIL_US']
                  ];
                  echo str_replace($find, $replacement, $cfg['ABSTRACT_SUBMISSION_SUCCESS_INFO']);
                  ?>

                  <a href="<?= _BASE_URL_ ?>profile.php" class="note-btns"><img src="images/arrow5.png" /> Visit
                    Profile</a>



                </div>
              </div>

            </div>
          </div>
        </div>
      </section>
      <?php include_once('footer.php'); ?>
      <!-- <footer class="main-footer">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6 foot-left">
            <ul>
              <li class="foot-btn" id="slide1" data-aos="zoom-in" data-aos-delay="200"
                data-aos-duration="600">
                <img src="images/lft-menu-big-tick.png" alt="" /><a href="#">Entitlements</a>
              </li>
              <li class="foot-btn" id="slide2" data-aos="zoom-in" data-aos-delay="400"
                data-aos-duration="600">
                <img src="images/lft-menu-rupees.png" alt="" /><a href="#">Refund Policy</a>
              </li>
              <li data-aos="zoom-in" data-aos-delay="600" data-aos-duration="600">
                <img src="images/lft-menu-email.png" alt="" /><a href="#">isncon2023@gmail.com</a>
              </li>
              <li data-aos="zoom-in" data-aos-delay="800" data-aos-duration="600">
                <img src="images/lft-menu-call.png" alt="" /><a href="#">7596071515</a>
              </li>
            </ul>
          </div>
          <div class="col-lg-6 p-0">
            <marquee>News Feed: This is dummy text. Please don’t read this. Thanks for not reading
            </marquee>
          </div>
        </div>
      </div>
    </footer> -->


    </main>
  </div>
</body>

</html>