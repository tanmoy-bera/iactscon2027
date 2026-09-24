<?php if ($resultSocialIcon[0]) { ?>
    <div class="media-icons" data-aos="flip-left" data-aos-duration="800">
        <div class="media-bottom">
            <?php
            foreach ($resultSocialIcon as $k => $valSocial) {
                $icon_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $valSocial['icon'];
            ?>
                <a href="<?= $valSocial['link']; ?>" target="_blank" class="media-hidden"><img src="<?= $icon_image ?>" alt="" /></a>
            <?php
            }
            $buttonIcon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSocialButtonIcon[0]['icon'];
            ?>
            <!--  <a href="#" class="media-hidden"><img src="<?= _BASE_URL_ ?>images/ic-med3.png" alt="" /></a>
      <a href="#" class="media-hidden"><img src="<?= _BASE_URL_ ?>images/ic-med.png" alt="" /></a>
      <a href="#" class="media-hidden"><img src="<?= _BASE_URL_ ?>images/ic-med2.png" alt="" /></a>
      <a href="#" class="media-hidden"><img src="<?= _BASE_URL_ ?>images/ic-med4.png" alt="" /></a> -->
            <span class="media-click"><img src="<?php echo $buttonIcon; ?>" width="50px" height="50px"></span>
        </div>
        <?php
        $sql                =    array();
        $sql['QUERY']    = "SELECT * 
                                            FROM " . _DB_COMPANY_INFORMATION_;
        $result            = $mycms->sql_select($sql);
        $social_icon_text    =    $result[0]['social_icon_text'];
        ?>
        <div class="media-label"><span><?= $social_icon_text ?></span></div>
    </div>
<?php } ?>

<div class="dashbord-tow-menu sidebar1">
    <div class="close"><img src="images/close.svg" alt=""></div>
    <div class="register-header">
        <a href="<?= _BASE_URL_ ?>profile.php"><img src="<?= _BASE_URL_ ?>images/cat-ic-1.png" alt="" /></a>
        <h3>Registration</h3>
    </div>
    <div class="register-header2">
        <ul>
            <li>
                <img src="<?= _BASE_URL_ ?>images/badge1.png" alt="" />
                <h5>Member.</h5>
            </li>
            <li>
                <img src="<?= _BASE_URL_ ?>images/badge2.png" alt="" />
                <h5>Registration ID</h5>
            </li>
        </ul>

        <a href="<?= _BASE_URL_ ?>registration.tariff.php?abstractDelegateId=<?= $delegateId ?>" class="btn">REGISTER NOW</a>
        <a href="<?= _BASE_URL_ ?>" class="btn">Log In</a>


    </div>

</div>