<?php
include_once("includes/frontend.init.php"); 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: Terms & Conditions | <?php echo $cfg['EMAIL_CONF_NAME']; ?> ::</title>
		
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		//backButtonOffJS();

        $sql = array();
                    $sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
                                            WHERE `status`='A' order by id desc limit 1";
                                      
        $result = $mycms->sql_select($sql);
        $row    = $result[0];

        $header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['header_image'];
        if($row['header_image']!='')
        {
            $emailHeader  = $header_image;
        }
        
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>terms_cond_refund_privacy.css" />
        <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>all.css" />
        <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>login_css.php?link_color=<?=$cfg['link_color']?>" />
	</head>
	<body> 
		<div class="container-fluied">
        <div class="container">
            <div class="row">                
				<?
				leftCommonMenu();
				?>
                <div class="col-xs-11 profileright-section">
                     <div class="banner-wrap">
                        <img src="<?php echo $emailHeader; ?>" alt="">
                    </div>
                    <div class="log-wrap"><h1>Terms & Conditions</h1></div>
                                      
                    <div class="t-c terms">
                        <!-- <h4 class="termhed">For Registration</h4>
                        <ul class="termlist">
                            <li>Every delegate will receive a registration confirmation mail after the successful completion of the payment of registration fee. Time span for receiving the mail may vary due to several reasons. If you do not receive any mail within 15 days of registration please mail at neocon2022@gmail.com
                            </li>
                            <li>Only registered persons can attend the conference. Replacement or substitution is not allowed.
                            </li>
                            <li>Registrants can make cancellation requests from their user profiles or may mail at neocon2022@gmail.com.
                            </li>
                        </ul>


                        <h4 class="termhed">FOR ACCOMMODATION</h4>
                        <ul class="termlist">
                            <li>Check-in: 12:00 hrs. Check-out: 12:00 hrs. Check-in/check-out timing will be followed strictly.
                            </li>
                            <li>Early check-in and late check-out are subject to room availability and as per the hotel policy.
                            </li>
                            <li>One must carry his/her ID PROOF at the time of check-in.
                            </li>

                            <li>All additional expenditures incurred on telephone calls, room service, wi-fi, laundry, mini bar, tobacco, additional cot, restaurant bills etc. should be settled by the guests before departure.</li>
                            <li>Extra room nights will be subject to room availability and as per the hotel policy. Room nights will be charged as per the hotel rate.</li>
                            <li>Organising committee holds the sole authority to select the co-occupant for every boarder who opts for twin sharing.
                            </li>
                        </ul>
                        
                        <p class="policytxt">The policy may be changed if situation demands. Please follow <a href="www.neocon2022.com">www.neocon2022.com</a> for time to time to keep yourself updated.</p> -->
                        <?php echo $cfg['TERMS_PAGE_INFO']; ?>
                    </div>				
                </div>
            </div>
        </div>
    </div>
	</body>
</html>