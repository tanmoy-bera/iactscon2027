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
$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
                                            WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
$result = $mycms->sql_select($sql);
$row             = $result[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
if ($row['header_image'] != '') {
  $emailHeader  = $header_image;
}

$sqlIcon  = array();
$sqlIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                      WHERE `status`='A' order by id ";
//$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$resultIcon = $mycms->sql_select($sqlIcon);

$title = 'Refund Policy';
?>
<!DOCTYPE html>
<html>

<?php
setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();
include_once('header.php'); ?>

<body class="single">
  <div class="body-frm">


      <?php include_once('sidebar_icon.php'); ?>

      <div class="mail-home back-btn">
        <?php if ($mycms->getSession('LOGGED.USER.ID') != "") { ?>
          <a href="<?= _BASE_URL_ ?>profile.php" class="btn btn-w prev"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
              <path d="M34.5 239L228.9 44.7c9.4-9.4 24.6-9.4 33.9 0l22.7 22.7c9.4 9.4 9.4 24.5 0 33.9L131.5 256l154 154.8c9.3 9.4 9.3 24.5 0 33.9l-22.7 22.7c-9.4 9.4-24.6 9.4-33.9 0L34.5 273c-9.4-9.4-9.4-24.6 0-33.9z" />
            </svg> Back</a>
        <?php } else { ?>
          <a href="<?= _BASE_URL_ ?>" class="btn btn-w prev"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
              <path d="M34.5 239L228.9 44.7c9.4-9.4 24.6-9.4 33.9 0l22.7 22.7c9.4 9.4 9.4 24.5 0 33.9L131.5 256l154 154.8c9.3 9.4 9.3 24.5 0 33.9l-22.7 22.7c-9.4 9.4-24.6 9.4-33.9 0L34.5 273c-9.4-9.4-9.4-24.6 0-33.9z" />
            </svg> Back</a>
        <?php } ?>
      </div>
  <section class="main-section refundPolicy-holder">
              <div class="inner-greadient-sec mt-0">
                <div class="refundPolicy-info">
                  <h2>Refund Policy</h2>

                  <?php echo $cfg['CANCELLATION_PAGE_INFO']; ?>
                </div>

              </div>
      </section>
      <?php include_once('footer.php'); ?>

  </div>
</body>

</html>