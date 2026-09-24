<?php
 include_once("includes/frontend.init.php"); 
// include_once("includes/function.registration.php");
// include_once("includes/function.workshop.php");
 include_once("includes/function.delegate.php");
// include_once("includes/function.invoice.php");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: Registration | <?php echo $cfg['EMAIL_CONF_NAME']; ?> ::</title>
		
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>registration.success.css" />
        <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>all.css" />
	</head>
	<body> 
		<div class="container-fluied">
        <div class="container">
            <div class="row">                
				<?
				leftCommonMenu();
				
				$userEmailId 	= $mycms->decoded($_REQUEST['email']);
				$delegateId 	= $mycms->decoded($_REQUEST['did']);
				$userDetails	= getUserDetails($delegateId);
				$slip_id = $mycms->decoded($_REQUEST['slip']);
				?>
                <div class="col-xs-11 profileright-section" style="width: 85%">
                    <div class="log"><h1>THANK TOU</h1></div>
                    <div class="summery"><h3>REGISTRATION PROCEDURE IS SUCCESSFUL</h3></div>                    
                    <div class="paynow-msg" style="margin-top: 25px;">
						<h5>Welcome to the <?=$cfg['EMAIL_CONF_NAME']?></h5>
						<h5>Registration Confirmation will be mailed to the registered E-mail id</h5>
						<h5>For any assistance or query, please write to us at <?=$cfg['EMAIL_CONF_EMAIL_US']?></h5>
                    </div>
                    <div class="bttn" style="margin-top: 25px;"><button onClick="window.location.href='profile.php'" style="background:#7f8080">Proceed to your Profile</button></div>
                </div>
				<?
				
				?>
            </div>
        </div>
    </div>
	</body>
</html>