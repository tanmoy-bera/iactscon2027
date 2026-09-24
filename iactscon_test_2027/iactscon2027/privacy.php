<?php
include_once("includes/frontend.init.php"); 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: Privacy Policy | <?php echo $cfg['EMAIL_CONF_NAME']; ?> ::</title>
		
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		//backButtonOffJS();

        $sql    =   array();
                    $sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
                                            WHERE `status`='A' order by id desc limit 1";
                                        
$result = $mycms->sql_select($sql);
$row             = $result[0];

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
                    <div class="log-wrap"><h1>Privacy Policy</h1></div>
                   
                    <div class="t-c" style="margin-top: 30px;">
                       
                        <?php echo $cfg['PRIVACY_PAGE_INFO']; ?>
                    </div>
                   	
                </div>
            </div>
        </div>
    </div>
	</body>
</html>